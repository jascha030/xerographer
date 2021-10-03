<?php

namespace Composer;

use Jascha030\Xerox\Composer\ComposerHelper;
use PHPUnit\Framework\TestCase;

class ComposerHelperTest extends TestCase
{
    public function testGetComposerRoot(): void
    {
        self::assertIsString(ComposerHelper::getComposerRoot());
    }
}
