<?php

namespace Jascha030\Xerox\Twig;

use Twig\Environment;

interface TemplaterInterface
{
    public function getEnvironment(): Environment;

    public function render(string $template, array $context = []): string;
}
