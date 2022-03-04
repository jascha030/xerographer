<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests\Functions\Helpers;

use PHPUnit\Framework\TestCase;
use function Jascha030\Xerox\Helpers\sanitizeProjectName;

/**
 * @internal
 * @coversNothing
 */
final class StringHelperTest extends TestCase
{
    public function testSanitizeProjectName(): void
    {
        $expected = 'nice-php8-project-name';

        $this->assertEquals($expected, sanitizeProjectName('NICE PHP8 \\\'Project NAME\\\''));
    }
}
