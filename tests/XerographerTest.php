<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests;

use Jascha030\Xerox\Xerographer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jascha030\Xerox\Application\Application
 * @internal
 */
final class XerographerTest extends TestCase
{
    public function testGetConfigurationFiles(): void
    {
        $configDir = dirname(__DIR__) . '/config';
        $dirs      = Xerographer::getConfigurationFiles();

        $this->assertEquals(
            [
                "{$configDir}/console.php",
                "{$configDir}/twig.php",
            ],
            $dirs
        );

        foreach ($dirs as $filename) {
            $this->assertFileExists($filename);
        }
    }
}
