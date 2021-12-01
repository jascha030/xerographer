<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests\Console\Command;

use Exception;
use Jascha030\Xerox\Application\Application;
use Jascha030\Xerox\Console\Command\InitCommand;
use Jascha030\Xerox\Database\DatabaseService;
use Jascha030\Xerox\Tests\TestDotEnvTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class InitCommandTest extends TestCase
{
    use TestDotEnvTrait;

    private const TEST_VALUES = [
        'DB_NAME'     => 'testdb',
        'DB_USER'     => 'user',
        'DB_PASSWORD' => 'test_password',
        'WP_HOME'     => 'https://example.test',
        'SALTS'       => 'SALTS="test"',
        'WP_DEBUG'    => true,
    ];

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

    private string $projectDir;

    private Filesystem $fileSystem;

    public function setUp(): void
    {
        $this->fileSystem = new Filesystem();
        $this->projectDir = dirname(__FILE__, 3) . '/Fixtures/testproject';

        $this->cleanTestProject();

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->cleanTestProject();

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testConstruct(): InitCommand
    {
        $container = $this->getContainer();
        $command = new InitCommand($container);

        self::assertInstanceOf(Command::class, $command);
        self::assertInstanceOf(InitCommand::class, $command);

        $command->setApplication($this->getApplication());

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
    public function testGetQuestionKey(InitCommand $command): void
    {
        self::assertEquals('init', $command->getQuestionKey());
    }

    /**
     * @depends testConstruct
     */
    public function testGetQuestionHelper(InitCommand $command): void
    {
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(QuestionHelper::class, $command->getQuestionHelper());
    }

    /**
     * @depends testConstruct
     */
    public function testSanitizeDatabaseName(InitCommand $command): void
    {
        self::assertEquals('testdb', $command->sanitizeDatabaseName('test db'));
    }

    /**
     * @depends testConstruct
     */
    public function testGetSalts(InitCommand $command): void
    {
        $salts = $command->getSalts();

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

    /**
     * @depends testConstruct
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testGenerateEnvContents(InitCommand $command): void
    {
        $testEnv  = dirname(__FILE__, 3) . '/Fixtures/Templates/test.env';
        $contents = $command->generateEnvContents(...array_values(self::TEST_VALUES));

        $testAgainst = file_get_contents($testEnv);

        self::assertEquals($testAgainst, $contents);
    }

    /**
     * @depends testConstruct
     * @depends testConfigure
     * @depends testSanitizeDatabaseName
     * @depends testGetSalts
     * @depends testGenerateEnvContents
     * @throws \Doctrine\DBAL\Exception
     */
    public function testExecute(InitCommand $command): void
    {
        $env         = $this->getDotEnv();
        $projectName = uniqid('unittest', false);

        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            $projectName,
            $env['DB_USER'],
            $env['DB_PASSWORD'],
            $projectName,
            $env['ROOT_PASSWORD']
        ]);

        self::assertEquals(0, $commandTester->execute(['command' => $command]));
        self::assertTrue($this->fileSystem->exists($this->projectDir . '/public/.env'));

        // Execute second time to assert failure.
        self::assertEquals(1, $commandTester->execute(['command' => $command]));

        $database = new DatabaseService($env['DB_USER'], $env['DB_PASSWORD']);
        $database->dropDatabase("wp_$projectName");

        $this->unlinkPublicDir($projectName);
    }

    private function getContainer(): ContainerInterface
    {
        return include dirname(__FILE__, 4) . '/includes/bootstrap.php';
    }

    private function getApplication(): Application
    {
        $app = new Application($this->getContainer());
        $app->setAutoExit(false);

        return $app;
    }

    private function cleanTestProject(): void
    {
        if (!isset($this->projectDir)) {
            $class = __CLASS__;

            throw new \RuntimeException(
                "`{$class}::cleanTestProject()` can't run before `{$class}::setUp()`."
            );
        }

        if ($this->fileSystem->exists($this->projectDir . '/public/.env')) {
            $this->fileSystem->remove($this->projectDir . '/public/.env');
        }
    }

    /**
     * Unlink symbolic link created by valet.
     */
    private function unlinkPublicDir(string $linkedName): void
    {
        $output   = new ConsoleOutput();
        $callback = static function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        };

        $link = Process::fromShellCommandline("valet unlink {$linkedName}");
        $link->setWorkingDirectory($this->projectDir . '/public');
        $link->run($callback);
    }
}
