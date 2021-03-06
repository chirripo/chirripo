<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * PhpInfo Command class.
 */
class PhpInfoCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('php-info')
            ->setAliases([
                'phpi',
            ])
            ->setDescription('Show PHP Info');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Executing: php -i in php container...\n"));

        $files = $this->setupFiles();
        $command = ['docker', 'exec', $_SERVER['PROJECT_NAME'] . '_php', 'php', '-i'];
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
