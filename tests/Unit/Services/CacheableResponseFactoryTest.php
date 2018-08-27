<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Services;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier\ParametersFactory
    as CacheValidatorIdentifierParametersFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheableResponseFactoryTest extends TestCase
{
    /**
     * @dataProvider createResponseDataProvider
     *
     * @param Request $request
     * @param array $parameters
     * @param CacheValidatorHeadersService $cacheValidatorHeadersService
     * @param string $expectedResponseEtag
     * @param int $expectedResponseStatusCode
     */
    public function testCreateResponse(
        Request $request,
        array $parameters,
        CacheValidatorHeadersService $cacheValidatorHeadersService,
        string $expectedResponseEtag,
        int $expectedResponseStatusCode
    ) {
        $cacheableResponseFactory = new CacheableResponseFactory(
            $cacheValidatorHeadersService,
            new CacheValidatorIdentifierParametersFactory()
        );

        $response = $cacheableResponseFactory->createResponse($request, $parameters);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertTrue($response->headers->getCacheControlDirective('public'));
        $this->assertTrue($response->headers->getCacheControlDirective('must-revalidate'));
        $this->assertEquals($expectedResponseEtag, $response->headers->get('etag'));
        $this->assertEquals($expectedResponseStatusCode, $response->getStatusCode());
    }

    public function createResponseDataProvider(): array
    {
        $etag = 'W/"ce2628dfb460f03c1a28e087f03828e5"';

        $request = new Request([], [], ['_route' => 'route_name',]);
        $parameters = [
            'route' => 'route_name',
        ];

        $requestWithIfNoneMatchHeader = clone $request;
        $requestWithIfNoneMatchHeader->headers->set('if-none-match', $etag);

        $cacheValidatorIdentifier = new CacheValidatorIdentifier($parameters);

        $cacheValidatorHeaders = new CacheValidatorHeaders();
        $cacheValidatorHeaders->setLastModifiedDate(new \DateTime());
        $cacheValidatorHeaders->setIdentifier($cacheValidatorIdentifier);

        return [
            'no existing CacheValidatorHeaders' => [
                'request' => $request,
                'parameters' => $parameters,
                'cacheValidatorHeadersService' => $this->createCacheValidatorHeadersService(
                    $cacheValidatorIdentifier,
                    null,
                    $cacheValidatorHeaders
                ),
                'expectedResponseEtag' => $etag,
                'expectedResponseStatusCode' => Response::HTTP_OK,
            ],
            'has existing CacheValidatorHeaders, no if-none-match header' => [
                'request' => $request,
                'parameters' => $parameters,
                'cacheValidatorHeadersService' => $this->createCacheValidatorHeadersService(
                    $cacheValidatorIdentifier,
                    $cacheValidatorHeaders
                ),
                'expectedResponseEtag' => $etag,
                'expectedResponseStatusCode' => Response::HTTP_OK,
            ],
            'has existing CacheValidatorHeaders, has if-none-match header' => [
                'request' => $requestWithIfNoneMatchHeader,
                'parameters' => $parameters,
                'cacheValidatorHeadersService' => $this->createCacheValidatorHeadersService(
                    $cacheValidatorIdentifier,
                    $cacheValidatorHeaders
                ),
                'expectedResponseEtag' => $etag,
                'expectedResponseStatusCode' => Response::HTTP_NOT_MODIFIED,
            ],
        ];
    }

    private function createCacheValidatorHeadersService(
        CacheValidatorIdentifier $cacheValidatorIdentifier,
        $findResponse,
        $createResponse = null
    ) {
        /* @var MockInterface|CacheValidatorHeadersService $cacheValidatorHeaderService */
        $cacheValidatorHeaderService = \Mockery::mock(CacheValidatorHeadersService::class);

        $cacheValidatorHeaderService
            ->shouldReceive('find')
            ->with((string)$cacheValidatorIdentifier)
            ->andReturn($findResponse);

        if ($createResponse) {
            $cacheValidatorHeaderService
                ->shouldReceive('create')
                ->withArgs(function (
                    string $passedCacheValidatorIdentifier,
                    \DateTime $datetime
                ) use ($cacheValidatorIdentifier) {
                    $this->assertEquals($cacheValidatorIdentifier, $passedCacheValidatorIdentifier);
                    $this->assertInstanceOf(\DateTime::class, $datetime);

                    return true;
                })
                ->andReturn($createResponse);
        }

        return $cacheValidatorHeaderService;
    }

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
