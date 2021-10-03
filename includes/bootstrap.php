<?php

use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/loader.php';

/**
 * @throws \Exception
 */
function getContainer(): ContainerInterface
{
    static $container;

    if (!isset($container)) {
        $containerBuilder = new ContainerBuilder(Container::class);
        $containerBuilder->addDefinitions(dirname(__DIR__) . '/config/container-config.php');
        $containerBuilder->useAutowiring(false);
        $containerBuilder->useAnnotations(false);

        $container = $containerBuilder->build();
    }

    return $container;
}
