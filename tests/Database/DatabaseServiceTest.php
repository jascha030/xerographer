<?php

namespace Jascha030\Xerox\Tests\Database;

use Jascha030\Xerox\Database\DatabaseService;
use Jascha030\Xerox\Database\DatabaseServiceInterface;
use Jascha030\Xerox\Tests\TestDotEnvTrait;
use PHPUnit\Framework\TestCase;

class DatabaseServiceInterfaceTest extends TestCase
{
    use TestDotEnvTrait;

    public function testConstructDatabaseService(): DatabaseServiceInterface
    {
        $env     = $this->getDotEnv();
        $service = new DatabaseService($env['DB_USER'], $env['DB_PASSWORD']);

        self::assertInstanceOf(DatabaseServiceInterface::class, $service);

        return $service;
    }

    /**
     * @depends testConstructDatabaseService
     */
    public function testCreateDatabase(DatabaseServiceInterface $service): void
    {
        $service->createDatabase();
    }
}
