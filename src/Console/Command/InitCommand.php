<?php

namespace Jascha030\Xerox\Console\Command;

use Exception;
use Jascha030\Xerox\Console\Question\AsksConsoleQuestionsTrait;
use Jascha030\Xerox\Database\DatabaseService;
use Jascha030\Xerox\Twig\TemplaterInterface;
use Jascha030\Xerox\Twig\TwigTemplater;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class InitCommand extends Command
{
    use AsksConsoleQuestionsTrait;

    private const SALTS_URL   = "https://api.wordpress.org/secret-key/1.1/salt";

    private const CONST_REGEX = "/define\('([A-Z_]*)',[ \t]*'(.*)'\);/";

    private bool $production;

    private string $directory;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
        $this->directory  = getcwd();
        $this->production = false;

        parent::__construct('init');
    }

    public function configure(): void
    {
        $this->setDescription('Init a new Environment with database.')
             ->addOption('production', 'p', InputOption::VALUE_NONE);
    }

    public function getQuestionKey(): string
    {
        return $this->getName();
    }

    public function getQuestionHelper(): QuestionHelper
    {
        return $this->getHelper('question');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->production = $input->getOption('production');
        $name             = $this->ask($input, $output, 'name');
        $database         = $this->sanitizeDatabaseName($name);
        $user             = $this->ask($input, $output, 'user');
        $password         = $this->ask($input, $output, 'password');

        $output->writeln('Creating database...');

        try {
            (new DatabaseService($user, $password))->createDatabase($database);
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $url = $this->ask($input, $output, 'url');

        try {
            $this->generateDotEnv($database, $user, $password, 'https://' . $url . '.test', $this->getSalts());
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $this->valetLink($url, $output);

        return Command::SUCCESS;
    }

    public function sanitizeDatabaseName(string $name): string
    {
        return preg_replace(
            '/[^A-Za-z0-9\-]/',
            '',
            str_replace(' ', '_', strtolower($name))
        );
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function generateEnvContents(string $database, string $user, string $password, string $url, string $salts): string
    {
        /** @var TwigTemplater $templater */
        $templater = $this->container->get(TemplaterInterface::class);

        return $templater->render(
            'env.twig',
            [
                'name'     => $database,
                'user'     => $user,
                'password' => $password,
                'url'      => $url,
                'salts'    => $salts,
                'debug'    => $this->production
                    ? 'false'
                    : 'true',
            ]
        );
    }

    /**
     * @todo: think about separation of concerns, does this belong in a Command class?
     */
    public function getSalts(): ?string
    {
        $resource = curl_init();
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($resource, CURLOPT_URL, self::SALTS_URL);
        $data = curl_exec($resource);
        curl_close($resource);

        preg_match_all(self::CONST_REGEX, $data, $matches);

        if (isset($matches[1], $matches[2])) {
            $salts = array_combine($matches[1], $matches[2]);

            ob_start();

            foreach ($salts as $key => $value) {
                echo $key . "=\"{$value}\"" . PHP_EOL;
            }

            return ob_get_clean();
        }

        return null;
    }

    protected function getQuestionContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function generateDotEnv(string $database, string $user, string $password, string $url, string $salts): void
    {
        $envString = $this->generateEnvContents($database, $user, $password, $url, $salts);
        $env       = "{$this->directory}/public/.env";

        if (! file_exists($env)) {
            (new Filesystem())->touch($env);

            if (! file_put_contents($env, $envString)) {
                throw new RuntimeException('Could not generate .env from template, check access rights.');
            }
        }
    }

    private function valetLink(string $domain, OutputInterface $output): void
    {
        $callback = static function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        };

        $link = Process::fromShellCommandline("valet link {$domain}");
        $link->setWorkingDirectory($this->directory . '/public');
        $link->run($callback);

        $secure = Process::fromShellCommandline('valet secure');
        $secure->setWorkingDirectory($this->directory . '/public');
        $secure->run($callback);
    }
}
