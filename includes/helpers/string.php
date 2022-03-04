<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Helpers;

function sanitizeProjectName(string $string): string
{
    /** @noinspection RegExpRedundantEscape */
    $regex = '([^\\w\\s\\d\\-_~,;\\[\\]\\(\\).])';

    return strtolower(str_replace(' ', '-', preg_replace($regex, '', $string)));
}
