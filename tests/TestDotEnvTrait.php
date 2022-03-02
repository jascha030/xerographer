<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests;

use Dotenv\Dotenv;

trait TestDotEnvTrait
{
    private function getDotEnv(): array
    {
        return Dotenv::createMutable(__DIR__)->load();
    }
}
