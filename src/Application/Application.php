<?php

namespace Jascha030\Xerox\Application;

use Jascha030\Xerox\Xerographer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

final class Application extends ConsoleApplication
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct(Xerographer::APP_NAME, Xerographer::APP_VERSION);
    }

    public function init(): void
    {
        foreach (Xerographer::APP_COMMANDS as $class) {
            $this->add(new $class($this->container));
        }
    }
}
