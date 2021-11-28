<?php

namespace Jascha030\Xerox\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigTemplater implements TemplaterInterface
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
     * @throws SyntaxError | RuntimeError | LoaderError
     */
    public function render(string $template, array $context = []): string
    {
        return $this
            ->getEnvironment()
            ->render($template, $context);
    }
}
