<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Url Command class.
 */
class PrintHostsEntriesCommand extends Command
{
    use ChirripoCommandTrait;

    protected $availableServices;

    protected function configure()
    {
        $this->availableServices = [
            'nginx',
            'varnish',
            'solr',
            'mailhog',
        ];
        $this->setName('hosts')
            ->setDescription('Get hosts entries if you are using proxy');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();

        $hosts_entry = '';

        if (!empty($_ENV['VIRTUAL_HOST'])) {
            $hosts_entry = '127.0.0.1 ';
            $hosts = [];
            foreach ($this->availableServices as $service) {
                if ($service === 'nginx') {
                    $hosts[] = $_ENV['VIRTUAL_HOST'];
                } else {
                    $hosts[] = $service . '.' . $_ENV['VIRTUAL_HOST'];
                }
            }
            $hosts_entry .= implode(' ', $hosts);
        }
        if (!empty($_ENV['OTHER_VIRTUAL_HOSTS'])) {
            $hosts_entry .= ' ' . $_ENV['OTHER_VIRTUAL_HOSTS'];
        }

        if ($hosts_entry) {
            $output->writeln(sprintf(
                "Add the following line to your hosts file (/etc/hosts for Linux and Mac)\n\n%s",
                $hosts_entry
            ));
        } else {
            $output->writeln(sprintf('You need to define VIRTUAL_HOST and optionally OTHER_VIRTUAL_HOSTS
              to use domain names'));
              exit(1);
        }
    }
}
