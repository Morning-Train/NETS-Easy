<?php

namespace Morningtrain\NETSEasy;

use Morningtrain\NETSEasy\Classes\NetsEasyPayment;
use Morningtrain\NETSEasy\DTOs\Payment;
use Morningtrain\NETSEasy\Exceptions\PaymentNotFoundException;

class NETSEasy
{
    public static function makeNetsEasyPaymentFromPaymentDTO(Payment $paymentDTO): NetsEasyPayment
    {
        return NetsEasyPayment::make()
            ->setPaymentDTO($paymentDTO);
    }

    /**
     * @throws PaymentNotFoundException
     */
    public static function makeNetsEasyPaymentFromPaymentId(string $paymentId): ?NetsEasyPayment
    {
        return NetsEasyPayment::make()
            ->setPaymentId($paymentId);
    }
}
