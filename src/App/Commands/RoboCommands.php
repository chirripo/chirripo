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
}
