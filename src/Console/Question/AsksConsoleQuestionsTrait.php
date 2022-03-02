<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Console\Question;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

trait AsksConsoleQuestionsTrait
{
    abstract public function getQuestionKey(): string;

    abstract public function getQuestionHelper(): QuestionHelper;

    abstract protected function getQuestionContainer(): ContainerInterface;

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function ask(InputInterface $input, OutputInterface $output, string $questionIdentifier)
    {
        return $this->getQuestionHelper()->ask($input, $output, $this->getQuestion($questionIdentifier));
    }

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function getQuestion(string $question): Question
    {
        $questionId = sprintf('command.%s.questions.%s', $this->getQuestionKey(), $question);

        return $this->getQuestionContainer()->get($questionId);
    }
}
