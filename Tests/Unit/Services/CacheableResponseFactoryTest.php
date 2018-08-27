<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Repository\CacheValidatorHeadersRepository;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class CacheableResponseFactoryTest extends TestCase
{
    /**
     * @dataProvider createResponseDataProvider
     *
     * @param Request $request
     * @param array $parameters
     * @param CacheValidatorIdentifierFactory $cacheValidatorIdentifierFactory
     * @param CacheValidatorHeadersService $cacheValidatorHeadersService
     */
    public function testCreateResponse(
        Request $request,
        array $parameters,
        CacheValidatorIdentifierFactory $cacheValidatorIdentifierFactory,
        CacheValidatorHeadersService $cacheValidatorHeadersService,
        string $expectedResponseEtag
    ) {
        /* @var MockInterface|UserManagerInterface $userManager */
        $userManager = \Mockery::mock(UserManagerInterface::class);

        $cacheableResponseFactory = new CacheableResponseFactory(
            $cacheValidatorHeadersService,
            $cacheValidatorIdentifierFactory,
            $userManager
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
        $parameters = [];
        $cacheValidatorHeaders = new CacheValidatorHeaders();
        $cacheValidatorHeaders
            ->setLastModifiedDate(new \DateTime());
        $cacheValidatorIdentifier = new CacheValidatorIdentifier();

        return [
            'no existing CacheValidatorHeaders' => [
                'request' => $request,
                'parameters' => $parameters,
                'cacheValidatorIdentifierFactory' => $this->createCacheValidatorHeaderFactory(
                    $request,
                    $parameters,
                    $cacheValidatorIdentifier
                ),
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
                'cacheValidatorIdentifierFactory' => $this->createCacheValidatorHeaderFactory(
                    $request,
                    $parameters,
                    $cacheValidatorIdentifier
                ),
                'cacheValidatorHeadersService' => $this->createCacheValidatorHeadersService(
                    $cacheValidatorIdentifier,
                    $cacheValidatorHeaders
                ),
                'expectedResponseEtag' => 'W/"d41d8cd98f00b204e9800998ecf8427e"',
            ],
        ];
    }

    private function createCacheValidatorHeaderFactory(
        Request $request,
        array $parameters,
        CacheValidatorIdentifier $createResponse
    ) {
        /* @var MockInterface|CacheValidatorIdentifierFactory $cacheValidatorIdentifierFactory */
        $cacheValidatorIdentifierFactory = \Mockery::mock(CacheValidatorIdentifierFactory::class);

        $cacheValidatorIdentifierFactory
            ->shouldReceive('create')
            ->with($request, $parameters)
            ->andReturn($createResponse);

        return $cacheValidatorIdentifierFactory;
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
