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
     */
    public function testCreateResponse(
        Request $request,
        array $parameters,
        CacheValidatorHeadersService $cacheValidatorHeadersService,
        string $expectedResponseEtag
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
    }

    public function createResponseDataProvider(): array
    {
        $request = new Request([], [], ['_route' => 'route_name',]);
        $parameters = [
            'route' => 'route_name',
        ];
        $cacheValidatorHeaders = new CacheValidatorHeaders();
        $cacheValidatorHeaders
            ->setLastModifiedDate(new \DateTime());
        $cacheValidatorIdentifier = new CacheValidatorIdentifier($parameters);

        return [
            'no existing CacheValidatorHeaders' => [
                'request' => $request,
                'parameters' => $parameters,
                'cacheValidatorHeadersService' => $this->createCacheValidatorHeadersService(
                    $cacheValidatorIdentifier,
                    null,
                    $cacheValidatorHeaders
                ),
                'expectedResponseEtag' => 'W/"d41d8cd98f00b204e9800998ecf8427e"',
            ],
            'has existing CacheValidatorHeaders' => [
                'request' => $request,
                'parameters' => $parameters,
                'cacheValidatorHeadersService' => $this->createCacheValidatorHeadersService(
                    $cacheValidatorIdentifier,
                    $cacheValidatorHeaders
                ),
                'expectedResponseEtag' => 'W/"d41d8cd98f00b204e9800998ecf8427e"',
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
