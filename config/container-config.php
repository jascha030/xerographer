<?php

use Jascha030\Xerox\Twig\TwigTemplater;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

return [
    LoaderInterface::class => DI\create()
    Environment::class => function (LoaderInterface $loader) {
        return new Environment($loader);
    },
    TwigTemplater::class => function (ContainerInterface $c) {
        return new TwigTemplater($c->get(Environment::class));
    }
];
