<?php

declare(strict_types=1);

use Symfony\Component\Console\Question\Question;
use function DI\create;

/**
 * Questions.
 */
return [
    'command.init.questions.name' => create(Question::class)
        ->constructor('Project name: '),
    'command.init.questions.user' => create(Question::class)
        ->constructor('Enter mysql username: '),
    'command.init.questions.password' => create(Question::class)
        ->constructor('Password: ')
        ->method('setHidden', true)
        ->method('setHiddenFallback', false),
    'command.init.questions.url' => create(Question::class)
        ->constructor('Enter url domain name: '),
];
