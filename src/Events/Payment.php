<?php

namespace Morningtrain\NETSEasy\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Morningtrain\NETSEasy\Model\PaymentReference;

abstract class Payment
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PaymentReference $paymentReference,
        public array $data = [],
    ) {}
}
