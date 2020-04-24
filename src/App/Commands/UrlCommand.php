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
class UrlCommand extends Command
{
    use ChirripoCommandTrait;

    protected $availableServices;

    protected function configure()
    {
        $this->availableServices = [
            'nginx' => 80,
            'varnish' => 81,
            'solr' => 83,
            'mailhog' => 25,
        ];
        $this->setName('url')
            ->setDescription('Get url for given service')
            ->setHelp(sprintf('Available services:%s', implode(', ', array_keys($this->availableServices))))
            ->addArgument('service', InputArgument::OPTIONAL, 'Pass the service to lookup the logs for.', 'nginx');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Getting url for service %s...\n", $input->getArgument('service')));

        $files = $this->setupFiles();

        $url = 'http://127.0.0.1:' . $_ENV['PORT_PREFIX'];

        $service = $input->getArgument('service');
        if (isset($this->availableServices[$service])) {
            $url .= $this->availableServices[$service];
            $output->writeln(sprintf('%s', $url));
        } else {
            $output->writeln(sprintf(
                "Service %s not defined",
                $service,
            ));
            exit(1);
        }
    }
}
