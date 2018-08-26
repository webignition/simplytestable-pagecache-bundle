<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FooCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (strpos($id, 'simplytestable_pagecache') === false) {
                continue;
            }

            $definition->setPublic(true);
        }

        foreach ($container->getAliases() as $id => $alias) {
            if (strpos($id, 'simplytestable_pagecache') === false) {
                continue;
            }

            $alias->setPublic(true);
        }
    }
}
