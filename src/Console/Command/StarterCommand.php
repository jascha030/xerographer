<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use function Jascha030\Xerox\Helpers\sanitizeProjectName;

final class StarterCommand extends Command
{
    public function __construct()
    {
        parent::__construct('starter');
    }

    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Project name.');
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $project = sanitizeProjectName($input->getArgument('name'));

        $process = Process::fromShellCommandline("`which composer` create-project jascha030/wp-starter {$project}", getcwd());
        $process->start();

        foreach ($process as $type => $line) {
            if (Process::ERR === $type) {
                $line = sprintf('<error>%s</error>', $line);
            }

            $output->writeln($line);
        }

        return $process->getExitCode();
    }
}
