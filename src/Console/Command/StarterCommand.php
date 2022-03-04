<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
        $name    = $input->getArgument('name');
        $project = sanitizeProjectName($name);
        $bar     = new ProgressBar($output, 100);
        $process = Process::fromShellCommandline("`which composer` create-project jascha030/wp-starter {$project}", getcwd());

        $output->writeln(sprintf('Creating new project <info>%s...</info>', $name));

        $process->start();
        $bar->start();

        $process->wait(static function (string $type, $buffer) use ($bar): void {
            $bar->advance();
        });

        $bar->finish();
        $code = $process->getExitCode();

        $output->writeln('');
        $output->writeln($code > 0 ? '<error>Something went wrong</error>' : 'Done!');

        return $code;
    }
}
