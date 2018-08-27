<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use SimplyTestable\PageCacheBundle\Command\ClearCommand;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractFunctionalTestCase extends TestCase
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp()
    {
        $this->kernel = new AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
        \Mockery::close();
    }
}