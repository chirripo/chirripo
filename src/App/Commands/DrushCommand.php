<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Drush Command class.
 */
class DrushCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('drush')
            ->setDescription('Executes drush command')
            ->setHelp('Execute given drush command inside the cli container')
            ->addArgument('drushCommand', InputArgument::IS_ARRAY, 'Pass the drush command to execute.', ['status']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();

        $drush_command = $input->getArgument('drushCommand');
        $docker_root = __DIR__ . '/../../../docker';

        $command = array_merge([
            'ssh',
            '-t',
            '-p',
            $_ENV['PORT_PREFIX'] . '22',
            '-o',
            'ForwardAgent=yes',
            '-o',
            'StrictHostKeyChecking=no',
            '-o',
            'UserKnownHostsFile=/dev/null',
            '-l',
            'root',
            'localhost',
            'drush',
        ], $drush_command);

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
