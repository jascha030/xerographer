<?php

declare(strict_types=1);

use Jascha030\Twig\Templater\TemplaterInterface;
use Jascha030\Twig\TwigService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use function DI\create;
use function DI\get;

return [
    'twig.root'               => dirname(__DIR__) . '/templates',
    LoaderInterface::class    => create(FilesystemLoader::class)->constructor(get('twig.root')),
    Environment::class        => create(Environment::class)->constructor(get(LoaderInterface::class)),
    TemplaterInterface::class => create(TwigService::class)->constructor(get(Environment::class)),
];
