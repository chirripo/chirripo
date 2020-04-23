<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Docker Compose Command class.
 */
class DockerComposeCommand extends Command
{
    protected function configure()
    {
        $this->setName('docker-compose')
            ->setAliases([
                'compose',
                'dc',
            ])
            ->setDescription('Execute docker-compose commands')
            ->setHelp('Execute given docker-compose commands in the right folder.')
            ->addArgument('composeCommand', InputArgument::REQUIRED, 'Pass the actual docker-compose command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Executing: docker-compose %s...\n", $input->getArgument('composeCommand')));

        $command = ['docker-compose'];
        $argument = $input->getArgument('composeCommand');
        $command_components = explode(' ', $argument);
        $command = array_merge($command, $command_components);
        $docker_root = __DIR__ . '/../../../docker';
        $process = new Process($command, $docker_root);
        $process->run();

        // Executes after the command finishes.
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}
