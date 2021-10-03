<?php

namespace Jascha030\Xerox\Tests\Composer;

use Jascha030\Xerox\Composer\ComposerHelper;
use PHPUnit\Framework\TestCase;

class ComposerHelperTest extends TestCase
{
    public function testGetComposerRoot(): void
    {
        self::assertIsString(ComposerHelper::getComposerRoot());
    }
}
