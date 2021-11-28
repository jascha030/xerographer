<?php

namespace Jascha030\Xerox\Tests\Console\Command;

use Exception;
use Jascha030\Xerox\Console\Command\InitCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

final class InitCommandTest extends TestCase
{
    private const WP_CONFIG_CONSTANTS = [
        'AUTH_KEY',
        'SECURE_AUTH_KEY',
        'LOGGED_IN_KEY',
        'NONCE_KEY',
        'AUTH_SALT',
        'SECURE_AUTH_SALT',
        'LOGGED_IN_SALT',
        'NONCE_SALT',
    ];

    /**
     * @throws Exception
     */
    public function testConstruct(): InitCommand
    {
        $container = include dirname(__FILE__, 4) . '/includes/bootstrap.php';
        $command   = new InitCommand($container);

        self::assertInstanceOf(Command::class, $command);
        self::assertInstanceOf(InitCommand::class, $command);

        return $command;
    }

    /**
     * @depends testConstruct
     */
    public function testConfigure(InitCommand $command): void
    {
        $description = 'Init a new Environment with database.';

        $command->setDescription('test');
        self::assertEquals('test', $command->getDescription());

        $command->configure();
        self::assertEquals($description, $command->getDescription());
    }

    /**
     * @depends testConstruct
     */
    public function testSanitizeDatabaseName(InitCommand $init): void
    {
        self::assertEquals('testdb', $init->sanitizeDatabaseName('test db'));
    }

    /**
     * @depends testConstruct
     */
    public function testGetSalts(InitCommand $init): void
    {
        $salts = $init->getSalts();

        self::assertIsString($salts);

        $lines = explode(PHP_EOL, $salts);
        self::assertCount(9, $lines);
        self::assertEquals('', $lines[8]);

        array_pop($lines);

        $constants = [];

        foreach ($lines as $line) {
            $constants[] = substr($line, 0, strpos($line, '='));
        }

        self::assertEquals(self::WP_CONFIG_CONSTANTS, $constants);
    }
}
