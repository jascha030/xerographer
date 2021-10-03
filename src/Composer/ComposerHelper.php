<?php

namespace Jascha030\Xerox\Composer;

use Composer\Factory;

class ComposerHelper
{
    public static function getComposerRoot(): string
    {
        $composerFile = Factory::getComposerFile();

        return dirname($composerFile, 4);
    }
}
