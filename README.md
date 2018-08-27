# Symfony 4 bundle for creating/validating cacheable responses

## Introduction

Easily make cacheable `Response` objects to be returned from your controllers for content that:

- can change 
- doesn't always change on every single request
- can be cached for an unknown amount of time

A created cacheable response has the following headers set:

- `Cache-Control: must-revalidate, public`
- `Last-Modified: <now>`
- `Etag: <hash of unique identifier>`

Subsequent requests made by a browser will include a `If-None-Match: <hash of unique identifier>` header which we can then use to determine if we should tell the browser to re-use the previously-cached response.

## Installation

### Install via composer:

Use composer to add as a project dependency:

    composer require simplytestable-pagecache-bundle:^0.1

### Update your database schema:

Details of cache-related headers previously sent are persisted to an object store. This bundle provides an entity, you need to update your database schema to allowt the entities to be persisted.

    ./bin/console doctrine:migrations:diff
    ./bin/console doctrine:migrations:migrate

## Usage

This bundle provides a `SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory` service. Use this as a type-hint in a controller constructor or in a controller action.

### Minimal usage

```php
<?php

namespace App\Controller;

use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ExampleController 
{
  public function exampleAction(
      CacheableResponseFactory $cacheableResponseFactory,
      Twig_Environment $twig,
      Request $request
  ): Response {
    // ... perform whatever operation your action requires
    
    // Create a cacheable response. This response is capable of being cached, but we don't
    // yet know if it is the correct response for the given request
    $response = CacheableResponseFactory->createResponse($request, []);
    
    // Check if the response can be returned as-is
    if (Response::HTTP_NOT_MODIFIED === $response->getStatusCode()) {
        return $response;
    }
    
    // The cached response cannot be returned as-is, it doesn't match what
    // the request is asking for. Render and return a response based on the response
    // already created
    $response->setContent($twig->render(
        '::base.html.twig',
        []
    ));
    
    return $response;
  }
}
```

### Uniquely identifiying a response

The request route (`$request->attributes->get('_route')` and the request accept header (`$request->headers->get('accept')` are used by default to determine the uniqueness of a request.

`CacheableResponseFactory::createResponse()` takes as its second argument an array of parameters that will be used in addition to the above. For example, if your page renders a blog post, you would want to add to the parameters the unique id of the post.

The array keys can be anything you like. The corresponding array values can be anything but they must be scalar types or objects that cast to scalars (such as an object with a `__toString()` method.

```php
<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ExampleController 
{
  public function exampleAction(
      $post_id,
      EntityManagerInterface $entityManager,
      CacheableResponseFactory $cacheableResponseFactory,
      Twig_Environment $twig,
      Request $request
  ): Response {
    $postRepository = $entityManager->getRepository(Post::class);
    $post = $postRepository->find($post_id);

    $response = CacheableResponseFactory->createResponse($request, [
      'post_id' => $post_id,
    ]);
    
    if (Response::HTTP_NOT_MODIFIED === $response->getStatusCode()) {
        return $response;
    }
    
    $response->setContent($twig->render(
        'post.html.twig',
        [
          'post' => $post,
        ]
    ));
    
    return $response;
  }
}
```

## Removing cache-related entities

Your controller actions will continue to return `304 Not Modified` responses regardless of whether the content has changed so long as the unique identifiers still match.

You will need to remove cache-related entities upon content changes. A good time to do so is right after deploying changes to production.

A command is provided to make this super easy:

    ./bin/console simplytestable:pagecache:cachvalidator:clear
    
You may have many, many, many cache-related entities to remove. These may take some time to process. The command will process removals in batches. The default batch size is 100.

You can specify whatever batch size you need:

    ./bin/console simplytestable:pagecache:cachevalidator:clear 1
    ./bin/console simplytestable:pagecache:cachevalidator:clear 10
    ./bin/console simplytestable:pagecache:cachevalidator:clear 345
    ./bin/console simplytestable:pagecache:cachevalidator:clear 1000

