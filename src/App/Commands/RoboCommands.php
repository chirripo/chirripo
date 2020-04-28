<?php

namespace Console\App\Commands;

use Robo\Result;
use Robo\Tasks;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboCommands extends Tasks
{
    use ChirripoCommandTrait;

    /**
     * Run ssh command on current site.
     *
     * Run ssh command on current site.
     *
     * @param array $cmd Array of arguments to create a full Drush command.
     */
    public function ssh(array $cmd)
    {
        $sshExec = $this->taskExec('ssh')->dir('./');
        $sshExec->option('-t');
        $sshExec->option('-p', $_ENV['PORT_PREFIX'] . '22');
        $sshExec->option('-o', 'ForwardAgent=yes');
        $sshExec->option('-o', 'StrictHostKeyChecking=no');
        $sshExec->option('-o', 'UserKnownHostsFile=/dev/null');
        $sshExec->option('-l', 'root');
        foreach ($cmd as $arg) {
            $sshExec->arg($arg);
        }
        return $sshExec->run();
    }

    /**
     * Run docker-compose command on current site.
     *
     * Run docker-compose command on current site.
     *
     * @param array $cmd Array of arguments to create a full Drush command.
     */
    public function compose(array $cmd)
    {
        $docker_root = __DIR__ . '/../../../docker';
        $files = $this->setupFiles();
        $dcExec = $this->taskExec('docker-compose')->dir($docker_root);
        foreach ($files as $index => $file) {
            if ($index % 2 === 0) {
                continue;
            }
            $dcExec->option('-f', $file);
        }
        foreach ($cmd as $arg) {
            $dcExec->arg($arg);
        }
        return $dcExec->run();
    }
}
