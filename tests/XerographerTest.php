<?php

namespace Jascha030\Xerox\Tests;

use Jascha030\Xerox\Xerographer;
use PHPUnit\Framework\TestCase;

class XerographerTest extends TestCase
{
    public function testGetConfigurationFiles(): void
    {
        $configDir = dirname(__DIR__) . '/config';
        $dirs      = Xerographer::getConfigurationFiles();

        self::assertEquals(
            [
                "{$configDir}/console.php",
                "{$configDir}/twig.php"
            ],
            $dirs
        );

        foreach ($dirs as $filename) {
            self::assertFileExists($filename);
        }
    }
}
