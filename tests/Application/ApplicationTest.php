<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests\Application;

use Jascha030\Xerox\Application\Application;
use Jascha030\Xerox\Console\Command\InitCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @covers \Jascha030\Xerox\Xerographer
 * @internal
 */
final class ApplicationTest extends TestCase
{
    public function testConstruct(): ResetInterface
    {
        $container = include dirname(__FILE__, 3) . '/includes/bootstrap.php';
        $this->assertInstanceOf(ContainerInterface::class, $container);

        $app = new Application($container);

        $this->assertInstanceOf(ResetInterface::class, $app);
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }

    /**
     * @depends testConstruct
     */
    public function testGetThrowsException(Application $app): void
    {
        $this->expectException(CommandNotFoundException::class);
        $app->get('init');
    }

    /**
     * @depends testConstruct
     * @depends testGetThrowsException
     */
    public function testAddXerographerCommands(Application $app): void
    {
        $app->addXerographerCommands();
        $initCommand = $app->get('init');

        $this->assertInstanceOf(InitCommand::class, $initCommand);
    }

    /**
     * @depends testConstruct
     */
    public function testRun(Application $app): void
    {
        $app->setAutoExit(false);

        $this->assertEquals(0, (new ApplicationTester($app))->run([]));
    }
}
