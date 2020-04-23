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
}
