<?php

namespace Morningtrain\NETSEasy\Exceptions;

class PaymentNotFoundException extends \Exception
{
    protected string $paymentId;

    public function __construct(
        string $paymentId,
        string $message = 'Payment not found',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->paymentId = $paymentId;
        parent::__construct($message, $code, $previous);
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }
}
