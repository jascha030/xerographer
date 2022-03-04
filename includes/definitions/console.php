<?php

declare(strict_types=1);

use Jascha030\Twig\Templater\TemplaterInterface;
use Jascha030\Xerox\Console\Command\InitCommand;
use Jascha030\Xerox\Console\Command\StarterCommand;
use Jascha030\Xerox\Xerographer;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Question\Question;
use function DI\create;
use function DI\get;

/**
 * Questions.
 */
return [
    'app'                             => create(Application::class)->constructor(Xerographer::APP_NAME, Xerographer::APP_VERSION)->method('addCommands', get('commands')),
    'command.init.questions.name'     => create(Question::class)->constructor('Project name: '),
    'command.init.questions.user'     => create(Question::class)->constructor('Enter mysql username: '),
    'command.init.questions.password' => create(Question::class)->constructor('Password: ')->method('setHidden', true)->method('setHiddenFallback', false),
    'command.init.questions.url'      => create(Question::class)->constructor('Enter url domain name: '),
    'commands'                        => [
        'init'    => create(InitCommand::class)->constructor(get(TemplaterInterface::class)),
        'starter' => create(StarterCommand::class),
    ],
];
