<?php

namespace Morningtrain\NETSEasy;

use Illuminate\Support\Str;
use Morningtrain\NETSEasy\Contracts\PaymentHandleWebhook;

class NETSEasyWordpressServiceProvider extends NETSEasyServiceProvider
{
    public function bootingPackage(): void
    {
        $this->app->singleton(\Morningtrain\NETSEasy\Wordpress\Http\PaymentHandleWebhook::class);
        $this->app->alias(\Morningtrain\NETSEasy\Wordpress\Http\PaymentHandleWebhook::class, PaymentHandleWebhook::class);

        if ($this->app->runningInConsole()) {
            $argv = $_SERVER['argv'] ?? [];

            /**
             * Check if we are running a migration command
             * Example: [
             *  "/usr/local/bin/wp"
             *  "--path=/app/wordpress"
             *  "artisan"
             *  "migrate"
             * ]
             */
            if (
                ! empty($argv[2]) && $argv[2] == 'artisan' &&
                ! empty($argv[3]) && Str::contains($argv[3], 'migrate')
            ) {
                $this->package->runsMigrations();
            }
        }

        parent::bootingPackage();
    }
}
