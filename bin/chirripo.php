<?php
if (\file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (\file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
}

use Symfony\Component\Console\Application;
use Consolidation\AnnotatedCommand\CommandFileDiscovery;

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

    $chirripo_directory = __DIR__ . '/../src/App/Commands';
    $chirripo_project_directory = __DIR__ . '/../../../../chirripo/App/Commands';

    $discovery = new CommandFileDiscovery();
    $discovery->setSearchPattern('*Command.php');
    $defaultCommandClasses = $discovery->discover("{$chirripo_directory}/", 'Console\App\Commands');

    $customCommandClasses = [];
    if (file_exists("{$chirripo_project_directory}/")) {
        $customCommandClasses = $discovery->discover("{$chirripo_project_directory}/", '\\Console\App\Commands');
    }

    $commandClasses = array_merge($defaultCommandClasses, $customCommandClasses);

    foreach ($commandClasses as $file => $class) {
        require_once($file);
        $app->add(new $class());
    }

    $app->run();
}
