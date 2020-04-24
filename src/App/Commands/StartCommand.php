<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Start Command class.
 */
class StartCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('start')
            ->setDescription('Start containers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Executing: docker-compose up -d...\n"));

        $files = $this->setupFiles();

        $commands = [
            array_merge(['docker-compose'], $files, ['up', '-d']),
            [
                'docker',
                'cp',
                '-a',
                $_SERVER['HOME'] . '/.ssh/id_rsa.pub',
                $_SERVER['PROJECT_NAME'] . '_cli:/root/.ssh/authorized_keys',
            ],
            ['docker', 'exec', $_SERVER['PROJECT_NAME'] . '_cli', 'chown', 'root:root', '/root/.ssh/authorized_keys'],
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
