<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Database;

use Doctrine\DBAL\Exception;

interface DatabaseServiceInterface
{
    /**
     * Create a mysql database.
     *
     * @throws Exception
     */
    public function createDatabase(string $name): void;
}
