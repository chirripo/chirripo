<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Robo\Runner;

/**
 * SSH Command class.
 */
class SshCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('ssh')
            ->setDescription('SSH into CLI container')
            ->setHelp('Create ssh session to CLI container.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();

        $command = [
            'localhost',
        ];

        $argv = $_SERVER['argv'];
        $argv = array_merge($argv, $command);
        var_dump($argv);

        $command_classes = ['Console\App\Commands\RoboCommands'];
        $runner = new Runner($command_classes);
        $output = new ConsoleOutput();
        $status_code = $runner->execute($argv, 'Chirripo', null, $output);
        exit($status_code);
    }
}
