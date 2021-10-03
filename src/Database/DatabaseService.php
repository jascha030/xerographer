<?php

namespace Jascha030\Xerox\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DatabaseService implements DatabaseServiceInterface
{
    private string $user;

    private string $password;

    private string $host;

    private string $driver;

    private Connection $connection;

    public function __construct(
        string $user,
        string $password,
        string $host = 'localhost',
        string $driver = 'pdo_mysql'
    ) {
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->driver = $driver;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function connect(): DatabaseServiceInterface
    {
        $params = [
            'user'     => $this->user,
            'password' => $this->password,
            'host'     => $this->host,
            'driver'   => $this->driver,
        ];

        $this->connection = DriverManager::getConnection($params);

        return $this;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function createDatabase(string $name): void
    {
        if (strpos($name, 'wp_') === false) {
            $name = 'wp_' . $name;
        }

        $this->connect()
            ->connection
            ->createSchemaManager()
            ->createDatabase($name);
    }
}
