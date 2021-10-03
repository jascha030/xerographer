<?php

namespace Jascha030\Xerox\Console\Command;

use Exception;
use Jascha030\Xerox\Database\DatabaseService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class InitCommand extends Command
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;

        parent::__construct('init');
    }

    public function configure(): void
    {
        $this->setDescription('Init a new Environment with database.')
             ->addArgument('name', InputArgument::OPTIONAL)
             ->addOption('production', 'p', InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name     = $input->getArgument('name');
        $database = $this->sanitizeDatabaseName($name);
        $user     = $this->ask($input, $output, 'user');
        $password = $this->ask($input, $output, 'password');

        try {
            (new DatabaseService($user, $password))->createDatabase($database);
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getQuestion(string $question): Question
    {
        return $this->container->get('command.init.questions.' . $question);
    }

    private function ask(InputInterface $input, OutputInterface $output, string $questionIdentifier)
    {
        $questionHelper = $this->getHelper('question');

        return $questionHelper->ask($input, $output, $this->getQuestion($questionIdentifier));
    }

    public function sanitizeDatabaseName(string $name): string
    {
        return preg_replace(
            '/[^A-Za-z0-9\-]/',
            '',
            str_replace(' ', '_', strtolower($name))
        );
    }
}
