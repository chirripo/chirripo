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
class XdebugEnableCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('xdebug-enable')
            ->setAliases([
                'xdebug',
                'xe',
            ])
            ->setDescription('Enable xdebug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $docker_root = __DIR__ . '/../../../docker';
        $this->setupEnv();
        $output->writeln(sprintf("Enabling xdebug...\n"));

        $files = $this->setupFiles();

        $platform = getenv("PLATFORM");
        $platform = ($platform == 'Darwin') ? 'mac' : 'linux';

        $commands = [
            array_merge(['docker-compose'], $files, [
                'exec',
                '-u',
                0,
                '-T',
                'php',
                'sh',
                '-c',
                'docker-php-ext-enable xdebug',
            ]),
            [
                'docker',
                'cp',
                $docker_root . '/php/xdebug-enable-' . $platform . '.ini',
                $_SERVER['PROJECT_NAME'] . '_php:/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
            ],
        ];

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
