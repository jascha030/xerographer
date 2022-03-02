<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Jascha030\Xerox\Database\DatabaseRemovalInterface;
use Jascha030\Xerox\Database\DatabaseService;
use Jascha030\Xerox\Database\DatabaseServiceInterface;
use Jascha030\Xerox\Tests\TestDotEnvTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Jascha030\Xerox\Database\DatabaseService
 */
final class DatabaseServiceTest extends TestCase
{
    use TestDotEnvTrait;

    private Connection $connection;

    public function testConstructDatabaseService(): DatabaseService
    {
        $env     = $this->getDotEnv();
        $service = new DatabaseService($env['DB_USER'], $env['DB_PASSWORD']);

        $this->assertInstanceOf(DatabaseServiceInterface::class, $service);
        $this->assertInstanceOf(DatabaseRemovalInterface::class, $service);

        return $service;
    }

    /**
     * @depends testConstructDatabaseService
     *
     * @throws Exception
     */
    public function testCreateDatabase(DatabaseServiceInterface $service): string
    {
        $connection   = $this->getConnection();
        $databaseName = uniqid('unittest', false);

        $connection->connect();
        $schemaManager = $connection->createSchemaManager();

        // First assert our database is not present beforehand.
        $this->assertArrayNotHasKey("wp_{$databaseName}", array_flip($schemaManager->listDatabases()));

        $service->createDatabase($databaseName);

        // Assert our database is present after creation.
        $this->assertArrayHasKey("wp_{$databaseName}", array_flip($schemaManager->listDatabases()));

        return "wp_{$databaseName}";
    }

    /**
     * @depends testConstructDatabaseService
     * @depends testCreateDatabase
     *
     * @throws Exception
     */
    public function testDropDatabase(DatabaseRemovalInterface $service, string $databaseName): void
    {
        $connection = $this->getConnection();

        $connection->connect();
        $schemaManager = $connection->createSchemaManager();

        // First assert our database IS present beforehand.
        $this->assertArrayHasKey($databaseName, array_flip($schemaManager->listDatabases()));

        $service->dropDatabase($databaseName);

        // Assert our database is absent after deletion.
        $this->assertArrayNotHasKey($databaseName, array_flip($schemaManager->listDatabases()));
    }

    /**
     * @throws Exception
     */
    private function getConnection(): Connection
    {
        $env = $this->getDotEnv();

        $params = [
            'user'     => $env['DB_USER'],
            'password' => $env['DB_PASSWORD'],
            'host'     => 'localhost',
            'driver'   => 'pdo_mysql',
        ];

        return DriverManager::getConnection($params);
    }
}
