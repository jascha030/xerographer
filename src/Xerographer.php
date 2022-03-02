<?php

declare(strict_types=1);

namespace Jascha030\Xerox;

use Jascha030\Xerox\Console\Command\InitCommand;

final class Xerographer
{
    /**
     * Console app version.
     */
    public const APP_VERSION = '1.0.0';

    /**
     * Console app name.
     */
    public const APP_NAME = 'Xerographer';

    /**
     * Console app commands.
     */
    public const APP_COMMANDS = [
        InitCommand::class,
    ];

    /**
     * Get definition files for the Container injected in the Console Application.
     *
     * @return string[]
     */
    public static function getConfigurationFiles(): array
    {
        $configDir = dirname(__DIR__) . '/config';

        return [
            "{$configDir}/console.php",
            "{$configDir}/twig.php",
        ];
    }
}
