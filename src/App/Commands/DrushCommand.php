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

        $drush_string = 'drush --root=/var/www/html ';
        $drush_string .= implode(' ', $drush_command);

        $command = [
            'ssh',
            'localhost',
            $drush_string,
        ];

        $argv = $_SERVER['argv'];
        array_splice($argv, 1, 1);
        $argv = array_merge($argv, $command);

        $command_classes = ['Console\App\Commands\RoboCommands'];
        $runner = new Runner($command_classes);
        $output = new ConsoleOutput();
        $status_code = $runner->execute($argv, 'Chirripo', null, $output);
        exit($status_code);
    }
}
