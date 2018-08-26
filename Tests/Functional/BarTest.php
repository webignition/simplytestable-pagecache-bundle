<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BarTest extends TestCase
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Connection
     */
    private $connection;

    protected function setUp()
    {
        $this->kernel = new AppKernel('test', true);
        $this->kernel->boot();
        $this->connection = $this->kernel->getContainer()->get('doctrine.dbal.default_connection');
    }

    public function testFoo()
    {
        var_dump('foo');
        $this->assertTrue(true);

        $cacheValidatorHeadersService = $this->kernel->getContainer()->get('simplytestable_pagecache.cache_validator_headers_service');
        var_dump(get_class($cacheValidatorHeadersService));

        $cacheValidatorIdentifierFactory = $this->kernel->getContainer()->get('simplytestable_pagecache.cache_validator_identifier_factory');
        var_dump(get_class($cacheValidatorIdentifierFactory));

        $cacheableResponseFactory = $this->kernel->getContainer()->get('simplytestable_pagecache.cacheable_response_factory');
        var_dump(get_class($cacheableResponseFactory));
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
    }
}