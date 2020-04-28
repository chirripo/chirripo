<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Robo\Runner;

/**
 * Docker Compose Command class.
 */
class DockerComposeCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('docker-compose')
            ->setAliases([
                'compose',
                'dc',
            ])
            ->setDescription('Execute docker-compose commands')
            ->setHelp('Execute given docker-compose commands in the right folder.')
            ->addArgument('composeCommand', InputArgument::IS_ARRAY, 'Pass the actual docker-compose command.', ['ps']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();

        $argv = $_SERVER['argv'];
        array_splice($argv, 1, 1, ['compose']);

        $command_classes = ['Console\App\Commands\RoboCommands'];
        $runner = new Runner($command_classes);
        $output = new ConsoleOutput();
        $status_code = $runner->execute($argv, 'Chirripo', null, $output);
        exit($status_code);

        echo $process->getOutput();
    }
}
