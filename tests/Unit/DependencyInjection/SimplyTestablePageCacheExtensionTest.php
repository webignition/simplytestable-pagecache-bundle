<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\DependencyInjection;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\DependencyInjection\SimplyTestablePageCacheExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SimplyTestablePageCacheExtensionTest extends TestCase
{
    public function testLoad()
    {
        /* @var MockInterface|ContainerBuilder $containerBuilder */
        $containerBuilder = \Mockery::mock(ContainerBuilder::class);
        $containerBuilder
            ->shouldReceive('fileExists')
            ->with(str_replace('tests/Unit', 'src', __DIR__ . '/../Resources/config/services.yml'))
            ->andReturn(true);

        $containerBuilder
            ->shouldReceive('setDefinition');

        $containerBuilder
            ->shouldReceive('setAlias');

        $extension = new SimplyTestablePageCacheExtension();
        $extension->load([], $containerBuilder);

        $this->addToAssertionCount(\Mockery::getContainer()->mockery_getExpectationCount());
    }

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
