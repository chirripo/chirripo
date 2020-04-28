<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * XdebugEnable Command class.
 */
class XdebugDisableCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('xdebug-disable')
            ->setAliases([
                'xd',
            ])
            ->setDescription('Disable xdebug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $docker_root = __DIR__ . '/../../../docker';
        $this->setupEnv();
        $output->writeln(sprintf("Disabling xdebug...\n"));

        $files = $this->setupFiles();

        $commands = [
            [
                'docker',
                'cp',
                $docker_root . '/php/xdebug-disable.ini',
                $_SERVER['PROJECT_NAME'] . '_php:/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
            ],
        ];
        $docker_root = __DIR__ . '/../../../docker';

        foreach ($commands as $index => $command) {
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
    }
}
