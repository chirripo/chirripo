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
 * Db Import Command class.
 */
class DbImportCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('db-import')
            ->setDescription('Executes db import command')
            ->setHelp('Import given db into default database.')
            ->addArgument('filepath', InputArgument::OPTIONAL, 'File to import')
            ->addArgument('dbname', InputArgument::OPTIONAL, 'The database in that we want to import the data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();

        $filepath = $input->getArgument('filepath');
        $databaseName = $_SERVER['MYSQL_DATABASE'];
        if ($optionalDatabaseName = $input->getArgument('dbname')) {
          $databaseName = $optionalDatabaseName;
        }
        $commands = [
            [
                'docker',
                'exec',
                $_SERVER['PROJECT_NAME'] . '_db',
                'mysql',
                '-u',
                $_SERVER['MYSQL_USER'],
                '-p' . $_SERVER['MYSQL_PASSWORD'],
                '-e',
                'DROP DATABASE IF EXISTS ' . $databaseName .
                    '; CREATE DATABASE ' . $databaseName . ';',
            ],
            [
                'pv',
                $filepath,
                '|',
                'gunzip',
                '-c',
                '|',
                'docker',
                'exec',
                '-i',
                $_SERVER['PROJECT_NAME'] . '_db',
                'mysql',
                '-u',
                $_SERVER['MYSQL_USER'],
                '-p' . $_SERVER['MYSQL_PASSWORD'],
                $databaseName . ';',
            ],
            [
                'gunzip',
                '-c',
                $filepath,
                '|',
                'docker',
                'exec',
                '-i',
                $_SERVER['PROJECT_NAME'] . '_db',
                'mysql',
                '-u',
                $_SERVER['MYSQL_USER'],
                '-p' . $_SERVER['MYSQL_PASSWORD'],
                $databaseName . ';',
            ],
        ];

        $docker_root = __DIR__ . '/../../../docker';

        foreach ($commands as $number => $command) {
            if ($number >= 1) {
                $temp_output = [];
                $return_code = 0;
                $command_string = implode(' ', $command);
                // Commands number 1 and 2 use piping and need to be run with exec.
                exec($command_string, $temp_output, $return_code);
                if ($number === 1) {
                    // Number 1 command is optional by using pv.
                    // Return code is badly set to 0 even if it fails.
                    // Output string will be empty if it fails.
                    $output_string = implode("\n", $temp_output);
                    if (!$return_code || ($return_code === 0 && !$output_string)) {
                        // Do not print output.
                        continue;
                    } else {
                        exit();
                    }
                }
                echo implode("\n", $temp_output);
                $output->writeln('Success');
            } else {
                // This will run only for command 0.
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
        }

        echo $process->getOutput();
    }
}
