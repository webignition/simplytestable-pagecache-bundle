<?php

namespace SimplyTestable\PageCacheBundle\Services;

use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier\ParametersFactory
    as CacheValidatorIdentifierParametersFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CacheableResponseFactory
{
    /**
     * @var CacheValidatorHeadersService
     */
    private $cacheValidatorHeadersService;

    /**
     * @var CacheValidatorIdentifierParametersFactory
     */
    private $cacheValidatorIdentifierParametersFactory;

    public function __construct(
        CacheValidatorHeadersService $cacheValidatorHeadersService,
        CacheValidatorIdentifierParametersFactory $cacheValidatorIdentifierParametersFactory
    ) {
        $this->cacheValidatorHeadersService = $cacheValidatorHeadersService;
        $this->cacheValidatorIdentifierParametersFactory = $cacheValidatorIdentifierParametersFactory;
    }

    public function createResponse(Request $request, array $parameters): Response
    {
        $cacheValidatorIdentifier = new CacheValidatorIdentifier(array_merge(
            $this->cacheValidatorIdentifierParametersFactory->createFromRequest($request),
            $parameters
        ));

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
