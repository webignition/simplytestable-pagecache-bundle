<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use SimplyTestable\PageCacheBundle\Command\ClearCommand;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;

class ContainerTest extends AbstractFunctionalTestCase
{
    /**
     * @dataProvider getServicesFromContainerDataProvider
     *
     * @param $serviceId
     * @param $expectedServiceClassName
     */
    public function testGetServicesFromContainer(string $serviceId, string $expectedServiceClassName)
    {
        $service = $this->container->get($serviceId);
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
            'ClearCommand id' => [
                'serviceId' => 'simplytestable_pagecache.command.cache_validator.clear',
                'expectedServiceClassName' => ClearCommand::class,
            ],
        ];
    }
}