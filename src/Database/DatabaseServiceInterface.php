<?php

namespace Jascha030\Xerox\Database;

use Doctrine\DBAL\Exception;

interface DatabaseServiceInterface
{
    public function __construct(
        string $user,
        string $password,
        string $host = 'localhost',
        string $driver = 'pdo_mysql'
    );

    /**
     * Create a mysql database.
     *
     * @throws Exception
     */
    public function createDatabase(string $name): void;
}
