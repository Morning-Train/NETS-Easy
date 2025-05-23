<?php

return [
    'auth-key' => env('NETS_EASY_AUTH_KEY'),
    'events' => [
        'payment.cancel.created' => \Morningtrain\NETSEasy\Events\PaymentCancelCreated::class,
        'payment.cancel.failed' => \Morningtrain\NETSEasy\Events\PaymentCancelFailed::class,
        'payment.charge.created.v2' => \Morningtrain\NETSEasy\Events\PaymentChargeCreatedV2::class,
        'payment.charge.failed' => \Morningtrain\NETSEasy\Events\PaymentChargeFailed::class,
        'payment.checkout.completed' => \Morningtrain\NETSEasy\Events\PaymentCheckoutCompleted::class,
        'payment.created' => \Morningtrain\NETSEasy\Events\PaymentCreated::class,
        'payment.refund.completed' => \Morningtrain\NETSEasy\Events\PaymentRefundCompleted::class,
        'payment.refund.failed' => \Morningtrain\NETSEasy\Events\PaymentRefundFailed::class,
        'payment.refund.initiated.v2' => \Morningtrain\NETSEasy\Events\PaymentRefundInitiatedV2::class,
        'payment.reservation.created.v2' => \Morningtrain\NETSEasy\Events\PaymentReservationCreatedV2::class,
        'payment.reservation.failed' => \Morningtrain\NETSEasy\Events\PaymentReservationFailed::class,
    ],
    'in_test_mode' => env('NETS_EASY_IN_TEST_MODE', true),
    'secret_key' => env('NETS_EASY_SECRET_KEY'),
];
