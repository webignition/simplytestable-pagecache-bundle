<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Tests\Functional\AbstractFunctionalTestCase;
use SimplyTestable\PageCacheBundle\Tests\Functional\EntityManagerProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheableResponseFactoryTest extends AbstractFunctionalTestCase
{
    public function testCreateResponse()
    {
        /* @var EntityManagerProxy $entityManagerProxy */
        $entityManagerProxy = $this->container->get(EntityManagerProxy::class);

        /* @var EntityManagerInterface|MockInterface $entityRepositoryMock */
        $entityRepositoryMock = $entityManagerProxy->getRepository(CacheValidatorHeaders::class);

        $entityRepositoryMock
            ->shouldReceive('findOneBy')
            ->with(['identifier' => 'ce2628dfb460f03c1a28e087f03828e5'])
            ->andReturn(null);

        /* @var CacheableResponseFactory $cacheableResponseFactory */
        $cacheableResponseFactory = $this->container->get(CacheableResponseFactory::class);

        $request = new Request([], [], [
            '_route' => 'route_name',
        ]);

        $request->headers->set('if-none-match', 'W/"ce2628dfb460f03c1a28e087f03828e5"');

        $parameters = [];

        $cacheableResponse = $cacheableResponseFactory->createResponse($request, $parameters);

        $this->assertInstanceOf(Response::class, $cacheableResponse);
        $this->assertEquals(Response::HTTP_NOT_MODIFIED, $cacheableResponse->getStatusCode());
    }
}
