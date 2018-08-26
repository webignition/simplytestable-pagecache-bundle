<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class ContainerTest extends TestCase
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    protected function setUp()
    {
        $this->kernel = new AppKernel('test', true);
        $this->kernel->boot();
    }

    /**
     * @dataProvider getServicesFromContainerDataProvider
     *
     * @param $serviceId
     * @param $expectedServiceClassName
     */
    public function testGetServicesFromContainer(string $serviceId, string $expectedServiceClassName)
    {
        $service = $this->kernel->getContainer()->get($serviceId);
        $this->assertInstanceOf($expectedServiceClassName, $service);
    }

    public function getServicesFromContainerDataProvider(): array
    {
        return [
            'CacheableResponseFactory id' => [
                'serviceId' => 'simplytestable_pagecache.cacheable_response_factory',
                'expectedServiceClassName' => CacheableResponseFactory::class,
            ],
            'CacheableResponseFactory alias' => [
                'serviceId' => CacheableResponseFactory::class,
                'expectedServiceClassName' => CacheableResponseFactory::class,
            ],
            'CacheValidatorHeadersService id' => [
                'serviceId' => 'simplytestable_pagecache.cache_validator_headers_service',
                'expectedServiceClassName' => CacheValidatorHeadersService::class,
            ],
            'CacheValidatorHeadersService alias' => [
                'serviceId' => CacheValidatorHeadersService::class,
                'expectedServiceClassName' => CacheValidatorHeadersService::class,
            ],
            'CacheValidatorIdentifierFactory id' => [
                'serviceId' => 'simplytestable_pagecache.cache_validator_identifier_factory',
                'expectedServiceClassName' => CacheValidatorIdentifierFactory::class,
            ],
            'CacheValidatorIdentifierFactory alias' => [
                'serviceId' => CacheValidatorIdentifierFactory::class,
                'expectedServiceClassName' => CacheValidatorIdentifierFactory::class,
            ],
        ];
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
    }
}