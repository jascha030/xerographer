<?php

namespace Jascha030\Xerox\Database;

interface DatabaseServiceInterface
{
    public function __construct(
        string $user,
        string $password,
        string $host = 'localhost',
        string $driver = 'pdo_mysql'
    );

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function createDatabase(string $name): void;
}
