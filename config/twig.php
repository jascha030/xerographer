<?php

use Jascha030\Xerox\Twig\TwigTemplater;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

return [
    'twig.root' => dirname(__DIR__) . '/templates',
    /**
     * Twig classes
     */
    LoaderInterface::class => static function (ContainerInterface $c) {
        return new FilesystemLoader($c->get('twig.root'));
    },
    Environment::class => static function (ContainerInterface $c) {
        return new Environment($c->get(LoaderInterface::class));
    },
    TwigTemplater::class => static function (ContainerInterface $c) {
        return new TwigTemplater($c->get(Environment::class));
    },
];