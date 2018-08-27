<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'console.command_loader' shared service.

return $this->services['console.command_loader'] = new \Symfony\Component\Console\CommandLoader\ContainerCommandLoader(new \Symfony\Component\DependencyInjection\ServiceLocator(array('console.command.about' => function (): \Symfony\Bundle\FrameworkBundle\Command\AboutCommand {
    return ($this->privates['console.command.about'] ?? $this->load('getConsole_Command_AboutService.php'));
}, 'console.command.assets_install' => function (): \Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand {
    return ($this->privates['console.command.assets_install'] ?? $this->load('getConsole_Command_AssetsInstallService.php'));
}, 'console.command.cache_clear' => function (): \Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand {
    return ($this->privates['console.command.cache_clear'] ?? $this->load('getConsole_Command_CacheClearService.php'));
}, 'console.command.cache_pool_clear' => function (): \Symfony\Bundle\FrameworkBundle\Command\CachePoolClearCommand {
    return ($this->privates['console.command.cache_pool_clear'] ?? $this->load('getConsole_Command_CachePoolClearService.php'));
}, 'console.command.cache_pool_delete' => function (): \Symfony\Bundle\FrameworkBundle\Command\CachePoolDeleteCommand {
    return ($this->privates['console.command.cache_pool_delete'] ?? $this->load('getConsole_Command_CachePoolDeleteService.php'));
}, 'console.command.cache_pool_prune' => function (): \Symfony\Bundle\FrameworkBundle\Command\CachePoolPruneCommand {
    return ($this->privates['console.command.cache_pool_prune'] ?? $this->load('getConsole_Command_CachePoolPruneService.php'));
}, 'console.command.cache_warmup' => function (): \Symfony\Bundle\FrameworkBundle\Command\CacheWarmupCommand {
    return ($this->privates['console.command.cache_warmup'] ?? $this->load('getConsole_Command_CacheWarmupService.php'));
}, 'console.command.config_debug' => function (): \Symfony\Bundle\FrameworkBundle\Command\ConfigDebugCommand {
    return ($this->privates['console.command.config_debug'] ?? $this->load('getConsole_Command_ConfigDebugService.php'));
}, 'console.command.config_dump_reference' => function (): \Symfony\Bundle\FrameworkBundle\Command\ConfigDumpReferenceCommand {
    return ($this->privates['console.command.config_dump_reference'] ?? $this->load('getConsole_Command_ConfigDumpReferenceService.php'));
}, 'console.command.container_debug' => function (): \Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand {
    return ($this->privates['console.command.container_debug'] ?? $this->load('getConsole_Command_ContainerDebugService.php'));
}, 'console.command.debug_autowiring' => function (): \Symfony\Bundle\FrameworkBundle\Command\DebugAutowiringCommand {
    return ($this->privates['console.command.debug_autowiring'] ?? $this->load('getConsole_Command_DebugAutowiringService.php'));
}, 'console.command.event_dispatcher_debug' => function (): \Symfony\Bundle\FrameworkBundle\Command\EventDispatcherDebugCommand {
    return ($this->privates['console.command.event_dispatcher_debug'] ?? $this->load('getConsole_Command_EventDispatcherDebugService.php'));
}, 'console.command.yaml_lint' => function (): \Symfony\Bundle\FrameworkBundle\Command\YamlLintCommand {
    return ($this->privates['console.command.yaml_lint'] ?? $this->load('getConsole_Command_YamlLintService.php'));
}, 'doctrine.database_create_command' => function (): \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand {
    return ($this->privates['doctrine.database_create_command'] ?? $this->load('getDoctrine_DatabaseCreateCommandService.php'));
}, 'doctrine.database_drop_command' => function (): \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand {
    return ($this->privates['doctrine.database_drop_command'] ?? $this->load('getDoctrine_DatabaseDropCommandService.php'));
}, 'doctrine.generate_entities_command' => function (): \Doctrine\Bundle\DoctrineBundle\Command\GenerateEntitiesDoctrineCommand {
    return ($this->privates['doctrine.generate_entities_command'] ?? $this->load('getDoctrine_GenerateEntitiesCommandService.php'));
}, 'doctrine.mapping_import_command' => function (): \Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand {
    return ($this->privates['doctrine.mapping_import_command'] ?? $this->load('getDoctrine_MappingImportCommandService.php'));
})), array('about' => 'console.command.about', 'assets:install' => 'console.command.assets_install', 'cache:clear' => 'console.command.cache_clear', 'cache:pool:clear' => 'console.command.cache_pool_clear', 'cache:pool:prune' => 'console.command.cache_pool_prune', 'cache:pool:delete' => 'console.command.cache_pool_delete', 'cache:warmup' => 'console.command.cache_warmup', 'debug:config' => 'console.command.config_debug', 'config:dump-reference' => 'console.command.config_dump_reference', 'debug:container' => 'console.command.container_debug', 'debug:autowiring' => 'console.command.debug_autowiring', 'debug:event-dispatcher' => 'console.command.event_dispatcher_debug', 'lint:yaml' => 'console.command.yaml_lint', 'doctrine:database:create' => 'doctrine.database_create_command', 'doctrine:database:drop' => 'doctrine.database_drop_command', 'doctrine:generate:entities' => 'doctrine.generate_entities_command', 'doctrine:mapping:import' => 'doctrine.mapping_import_command'));