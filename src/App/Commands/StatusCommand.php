<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Status Command class.
 */
class StatusCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('status')
            ->setAliases([
                'ps',
            ])
            ->setDescription('Status of the containers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Executing: docker-compose ps...\n"));

        $files = $this->setupFiles();

        $commands = [
            array_merge(['docker-compose', '-p', $_ENV['PROJECT_NAME']], $files, ['ps']),
        ];
        $docker_root = __DIR__ . '/../../../docker';

        foreach ($commands as $command) {
            $process = new Process($command, $docker_root);
            $process->setTimeout(300);
            $process->run();

            // Executes after the command finishes.
            if (!$process->isSuccessful()) {
                $output->writeln(sprintf(
                    "\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
                    $process->getOutput(),
                    $process->getErrorOutput()
                ));
                exit(1);
            }
        }

        echo $process->getOutput();

        $output->writeln(sprintf("The site is now available at http://localhost:%s80/", $_SERVER['PORT_PREFIX']));
    }
}
