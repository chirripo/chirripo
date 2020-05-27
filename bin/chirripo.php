<?php
if (\file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (\file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
}

use Symfony\Component\Console\Application;
use Console\App\Commands\DockerComposeCommand;
use Console\App\Commands\StartCommand;
use Console\App\Commands\StopCommand;
use Console\App\Commands\DownCommand;
use Console\App\Commands\SshCommand;
use Console\App\Commands\StatusCommand;
use Console\App\Commands\RestartCommand;
use Console\App\Commands\DockerLogsCommand;
use Console\App\Commands\UrlCommand;
use Console\App\Commands\DrushCommand;
use Console\App\Commands\MysqlCommand;
use Console\App\Commands\DbImportCommand;
use Console\App\Commands\PhpModulesCommand;
use Console\App\Commands\PhpInfoCommand;
use Console\App\Commands\PrintHostsEntries;

define('CHIRRIPO_VERSION', '1.0');

/**
 * Chirripo version.
 */
function chirripo_version() {
    return CHIRRIPO_VERSION;
}

/**
 * Chirripo entrypoint.
 */
function chirripo_main() {
    $app = new Application();
    $app->setName('Chirripo CLI Tool');
    $app->add(new DockerComposeCommand());
    $app->add(new StartCommand());
    $app->add(new StopCommand());
    $app->add(new DownCommand());
    $app->add(new SshCommand());
    $app->add(new RestartCommand());
    $app->add(new StatusCommand());
    $app->add(new DockerLogsCommand());
    $app->add(new UrlCommand());
    $app->add(new DrushCommand());
    $app->add(new MysqlCommand());
    $app->add(new DbImportCommand());
    $app->add(new PhpModulesCommand());
    $app->add(new PhpInfoCommand());
    $app->add(new PrintHostsEntries());

    $app->run();
}
