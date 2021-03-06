<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Docker Logs Command class.
 */
class DockerLogsCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('logs')
            ->setAliases([
                'log',
            ])
            ->setDescription('Execute docker-compose logs commands')
            ->setHelp('Execute given docker-compose logs in the right container.')
            ->addArgument('service', InputArgument::OPTIONAL, 'Pass the service to lookup the logs for.', 'nginx');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Executing: docker-compose logs %s...\n", $input->getArgument('service')));

        $files = $this->setupFiles();

        $command = array_merge(['docker-compose', '-p', $_ENV['PROJECT_NAME']], $files);
        $service = $input->getArgument('service');
        $command = array_merge($command, ['logs', $service]);
        $docker_root = __DIR__ . '/../../../docker';
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

        echo $process->getOutput();
    }
}
