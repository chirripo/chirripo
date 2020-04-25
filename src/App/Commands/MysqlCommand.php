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
 * Mysql Command class.
 */
class MysqlCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('mysql')
            ->setDescription('Executes mysql command')
            ->setHelp('Execute given mysql query inside the cli container')
            ->addArgument('query', InputArgument::OPTIONAL, 'Pass the mysql query to execute.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $query = $input->getArgument('query');
        $docker_root = __DIR__ . '/../../../docker';

        $mysql_string = 'cd /var/www/html ;  mysql -h db -u root -p' . $_SERVER['MYSQL_ROOT_PASSWORD'] . 
            ' ' . $_SERVER['MYSQL_DATABASE'];
        if ($query) {
            $mysql_string .= ' -e "' . $query . '"';
        }
        $command = [
            'ssh',
            'localhost',
            $mysql_string,
        ];

        $arg_count = count($_SERVER['argv']);
        $argv = $_SERVER['argv'];
        array_splice($argv, 1, $arg_count);
        $argv = array_merge($argv, $command);

        $command_classes = ['Console\App\Commands\RoboCommands'];
        $runner = new Runner($command_classes);
        $output = new ConsoleOutput();
        $status_code = $runner->execute($argv, 'Chirripo', null, $output);
        exit($status_code);
    }
}
