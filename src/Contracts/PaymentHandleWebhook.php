<?php

namespace Morningtrain\NETSEasy\Contracts;

interface PaymentHandleWebhook
{
    public function url(string $event = '{event}'): string;

    public function register(): void;
}
