<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Stop Command class.
 */
class StopCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('stop')
            ->setDescription('Stop containers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Executing: docker-compose stop...\n"));

        $files = $this->setupFiles();
        $command = array_merge(['docker-compose', '-p', $_ENV['PROJECT_NAME']], $files, ['stop']);
        $docker_root = __DIR__ . '/../../../docker';
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

        echo $process->getOutput();
    }
}
