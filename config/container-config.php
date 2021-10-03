<?php

use Jascha030\Xerox\Database\DatabaseServiceInterface;
use Jascha030\Xerox\Twig\TwigTemplater;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Question\Question;
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
    /**
     * Questions
     */
    'command.init.questions.name' => static function () {
        return new Question('Project name: ');
    },
    'command.init.questions.user' => static function () {
        return new Question('Enter mysql username: ');
    },
    'command.init.questions.password' => static function () {
        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        return $question;
    },
    'command.init.questions.url' => static function () {
        return new Question('Enter url domain name: ');
    }
];
