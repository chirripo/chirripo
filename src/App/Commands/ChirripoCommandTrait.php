<?php

namespace Console\App\Commands;

use Symfony\Component\Dotenv\Dotenv;

/**
 * Chirripo Command trait.
 */
trait ChirripoCommandTrait
{
    /**
     * Setup Env vars.
     */
    protected function setupEnv()
    {
        $dotenv = new Dotenv();
        if (\file_exists(__DIR__ . '/../../../.env')) {
            $dotenv->load(__DIR__ . '/../../../.env');
        } elseif (\file_exists(__DIR__ . '/../../../../../../.env')) {
            $dotenv->load(__DIR__ . '/../../../../../../.env');
        }
    }

    /**
     * Return files sintax to be passed to docker-compose commands.
     */
    protected function setupFiles()
    {
        $optional_services = [
            'solr',
            'memcached',
            'selenium',
            'varnish',
            'mailhog',
        ];

        $files = [
            '-f',
            'docker-compose.yml',
        ];

        foreach ($optional_services as $service) {
            if (!empty($_SERVER[\strtoupper($service) . '_ENABLE'])) {
                $files[] = '-f';
                $files[] = 'docker-compose.' . $service . '.yml';
            }
        }
        return $files;
    }
}
