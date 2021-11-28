<?php

namespace Jascha030\Xerox\Tests\Twig;

use Jascha030\Xerox\Twig\TemplaterInterface;
use Jascha030\Xerox\Twig\TwigTemplater;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigTemplaterTest extends TestCase
{
    public function testConstruct(): TemplaterInterface
    {
        $fileSystem  = new FilesystemLoader(dirname(__DIR__) . '/Fixtures/Twig');
        $environment = new Environment($fileSystem);
        $templater   = new TwigTemplater($environment);

        self::assertInstanceOf(TemplaterInterface::class, $templater);

        return $templater;
    }

    /**
     * @depends testConstruct
     */
    public function testRender(TemplaterInterface $templater): void
    {
        $renderedOutput = $templater->render('test-template.twig', [
            'location' => 'world'
        ]);

        self::assertIsString($renderedOutput);
        self::assertEquals('<p>Hello world!</p>' . PHP_EOL, $renderedOutput);
    }

    /**
     * @depends testConstruct
     */
    public function testGetEnvironment(TemplaterInterface $templater): void
    {
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(Environment::class, $templater->getEnvironment());
    }
}
