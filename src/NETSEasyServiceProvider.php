<?php

namespace Morningtrain\NETSEasy;

use Morningtrain\NETSEasy\Contracts\PaymentHandleWebhook;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NETSEasyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nets-easy')
            ->hasConfigFile()
            ->hasMigration('create_nets_easy_payment_references_table');

        $this->app->singleton(NETSEasy::class);
        \Morningtrain\NETSEasy\Facades\NETSEasy::setFacadeApplication($this->app);
    }

    public function bootingPackage(): void
    {
        $this->app->make(PaymentHandleWebhook::class)->register();
    }
}
