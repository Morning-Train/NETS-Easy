<?php

namespace Morningtrain\NETSEasy;

use Morningtrain\NETSEasy\Contracts\PaymentHandleWebhook;

class NETSEasyWordpressServiceProvider extends NETSEasyServiceProvider
{
    public function bootingPackage(): void
    {
        $this->app->singleton(\Morningtrain\NETSEasy\Wordpress\Http\PaymentHandleWebhook::class);
        $this->app->alias(\Morningtrain\NETSEasy\Wordpress\Http\PaymentHandleWebhook::class, PaymentHandleWebhook::class);

        parent::bootingPackage();
    }
}
