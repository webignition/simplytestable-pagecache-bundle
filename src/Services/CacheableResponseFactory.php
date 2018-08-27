<?php

namespace SimplyTestable\PageCacheBundle\Services;

use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier\Factory as CacheValidatorIdentifierFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier\ParametersFactory
    as CacheValidatorIdentifierParametersFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class CacheableResponseFactory
{
    /**
     * @var CacheValidatorHeadersService
     */
    private $cacheValidatorHeadersService;

    /**
     * @var CacheValidatorIdentifierFactory
     */
    private $cacheValidatorIdentifierFactory;

    /**
     * @var CacheValidatorIdentifierParametersFactory
     */
    private $cacheValidatorIdentifierParametersFactory;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(
        CacheValidatorHeadersService $cacheValidatorHeadersService,
        CacheValidatorIdentifierFactory $cacheValidatorIdentifierFactory,
        CacheValidatorIdentifierParametersFactory $cacheValidatorIdentifierParametersFactory,
        UserManagerInterface $userManager
    ) {
        $this->cacheValidatorHeadersService = $cacheValidatorHeadersService;
        $this->cacheValidatorIdentifierFactory = $cacheValidatorIdentifierFactory;
        $this->cacheValidatorIdentifierParametersFactory = $cacheValidatorIdentifierParametersFactory;
        $this->userManager = $userManager;
    }

    public function createResponse(Request $request, array $parameters): Response
    {
        $cacheValidatorIdentifier = $this->cacheValidatorIdentifierFactory->create($request, $parameters);
        $cacheValidatorHeaders = $this->cacheValidatorHeadersService->find($cacheValidatorIdentifier);

        if (empty($cacheValidatorHeaders)) {
            $cacheValidatorHeaders = $this->cacheValidatorHeadersService->create(
                $cacheValidatorIdentifier,
                new \DateTime()
            );
        }

        $response = new Response();
        $response->setPublic();
        $response->setEtag($cacheValidatorHeaders->getETag(), true);
        $response->setLastModified(new \DateTime($cacheValidatorHeaders->getLastModifiedDate()->format('c')));
        $response->headers->addCacheControlDirective('must-revalidate', true);

        $currentIfNoneMatch = $request->headers->get('if-none-match');
        $modifiedEtag = preg_replace('/-gzip"$/', '"', $currentIfNoneMatch);
        $request->headers->set('if-none-match', $modifiedEtag);

        return $response;
    }
}
