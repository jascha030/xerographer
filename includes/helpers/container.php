<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Helpers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 * Scan the ./definitions directory for definition files.
 *
 * @noinspection PhpUnnecessaryLocalVariableInspection
 */
function definitions(?string $dir = null): \Generator
{
    $dir ?? $dir = dirname(__DIR__) . '/definitions';
    $files       = array_diff(scandir($dir), ['..', '.']);

    foreach ($files as $file) {
        if (! str_ends_with($file, '.php')) {
            continue;
        }

        $basename = str_replace('.php', '', $file);

        yield $basename => "{$dir}/{$file}";
    }
}

/**
 * Bootstrap a new Di Container.
 *
 * @noinspection PhpUnhandledExceptionInspection
 */
function createContainer(): ContainerInterface
{
    $builder = (new ContainerBuilder())
        ->useAutowiring(false)
        ->useAnnotations(false);

    foreach (definitions() as $definition) {
        $builder->addDefinitions($definition);
    }

    return $builder->build();
}

function container(): ContainerInterface
{
    static $container;

    return $container ?? $container = createContainer();
}
