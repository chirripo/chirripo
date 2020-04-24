<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * SSH Command class.
 */
class SshCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('ssh')
            ->setDescription('Print ssh command')
            ->setHelp('You could also enclose the call of this command into backticks to immediately execute it.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();

        $command = [
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
        ];

        $command_string = implode(' ', $command);
        $output->writeln(sprintf(
            "%s\n",
            $command_string,
        ));
    }
}
