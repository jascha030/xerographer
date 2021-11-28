<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;

/**
 * Loader loads autoload.php, locally or from $HOME/.composer/vendor/autoload.php.
 */
require_once __DIR__ . '/loader.php';

/**
 * Create ContainerBuilder.
 */
$containerBuilder = new ContainerBuilder(Container::class);

/**
 * Add Container definition, config files.
 */
$containerBuilder->addDefinitions([
    dirname(__DIR__) . '/config/console.php',
    dirname(__DIR__) . '/config/twig.php'
]);

/**
 * Set Container settings, for Application.
 */
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);

/**
 * Build and return Container.
 *
 * @noinspection PhpUnhandledExceptionInspection
 * @return Container
 */
return $containerBuilder->build();
