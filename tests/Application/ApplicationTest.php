<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests\Application;

use Jascha030\Xerox\Console\Command\InitCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Contracts\Service\ResetInterface;
use function Jascha030\Xerox\Helpers\container;

/**
 * @covers \Jascha030\Xerox\Xerographer
 *
 * @internal
 */
final class ApplicationTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function testConstruct(): ResetInterface
    {
        $app = container()->get('app');

        $this->assertInstanceOf(ResetInterface::class, $app);
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }

    /**
     * @depends testConstruct
     */
    public function testAddXerographerCommands(Application $app): void
    {
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
