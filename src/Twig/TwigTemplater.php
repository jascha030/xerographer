<?php

namespace Jascha030\Xerox\Twig;

use Twig\Environment;

class TwigTemplater
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @throws \Twig\Error\SyntaxError | \Twig\Error\RuntimeError | \Twig\Error\LoaderError
     */
    public function render(string $template, array $context = []): string
    {
        return $this->getEnvironment()->render($template, $context);
    }
}
