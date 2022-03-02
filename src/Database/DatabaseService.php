<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class DatabaseService implements DatabaseServiceInterface, DatabaseRemovalInterface
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
        $this->user     = $user;
        $this->password = $password;
        $this->host     = $host;
        $this->driver   = $driver;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function createDatabase(string $name): void
    {
        if (false === strpos($name, 'wp_')) {
            $name = 'wp_' . $name;
        }

        $this->connect()
            ->connection
            ->createSchemaManager()
            ->createDatabase($name);

        $this->connection->close();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function dropDatabase(string $name): void
    {
        $this->connect()
            ->connection
            ->createSchemaManager()
            ->dropDatabase($name);

        $this->connection->close();
    }

    /**
     * @throws Exception
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
}
