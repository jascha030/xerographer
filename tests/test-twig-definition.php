<?php

use Composer\Package\Loader\LoaderInterface;
use Jascha030\Xerox\Twig\TemplaterInterface;
use Jascha030\Xerox\Twig\TwigTemplater;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    'twig.root' => dirname(__DIR__),
    /**
     * Twig classes
     */
    LoaderInterface::class => static function (ContainerInterface $c) {
        return new FilesystemLoader($c->get('twig.root'));
    },
    Environment::class => static function (ContainerInterface $c) {
        return new Environment($c->get(LoaderInterface::class));
    },
    TemplaterInterface::class => static function (ContainerInterface $c) {
        return new TwigTemplater($c->get(Environment::class));
    },
];
