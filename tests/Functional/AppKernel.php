<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use SimplyTestable\PageCacheBundle\SimplyTestablePageCacheBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SimplyTestablePageCacheBundle(),
        ];
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
        $loader->load(__DIR__.'/services.yml');
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container)
            {
                foreach ($container->getDefinitions() as $id => $definition) {
                    if (strpos($id, 'simplytestable_pagecache') === false) {
                        continue;
                    }

                    $definition->setPublic(true);
                }

                foreach ($container->getAliases() as $id => $alias) {
                    if (strpos($id, 'SimplyTestable\\PageCacheBundle\\') === false) {
                        continue;
                    }

                    $alias->setPublic(true);
                }
            }
        });

        return $container;
    }
}
