<?php

namespace Jascha030\Xerox\Tests\Console\Command;

use Jascha030\Xerox\Console\Command\InitCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class InitCommandTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test__construct(): InitCommand
    {
        require_once dirname(__FILE__, 4) . '/includes/bootstrap.php';

        $container = getContainer();
        $command   = new InitCommand($container);

        self::assertInstanceOf(Command::class, $command);
        self::assertInstanceOf(InitCommand::class, $command);

        return $command;
    }

    /**
     * @depends test__construct
     */
    public function testSanitizeDatabaseName(InitCommand $init)
    {
    }

    /**
     * @depends test__construct
     */
    public function testGetSalts(InitCommand $init): void
    {
        $salts = $init->getSalts();

        self::assertIsString($salts);
        // print_r($salts);
        // die();
    }

}
