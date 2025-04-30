<?php

return [
    'auth-key' => env('NETS_EASY_AUTH_KEY'),
    'events' => [
        'payment.cancel.created' => \Morningtrain\NETSEasy\Events\WebhookPaymentCancelCreated::class,
        'payment.cancel.failed' => \Morningtrain\NETSEasy\Events\WebhookPaymentCancelFailed::class,
        'payment.charge.created.v2' => \Morningtrain\NETSEasy\Events\WebhookPaymentChargeCreatedV2::class,
        'payment.charge.failed' => \Morningtrain\NETSEasy\Events\WebhookPaymentChargeFailed::class,
        'payment.checkout.completed' => \Morningtrain\NETSEasy\Events\WebhookPaymentCheckoutCompleted::class,
        'payment.created' => \Morningtrain\NETSEasy\Events\WebhookPaymentCreated::class,
        'payment.refund.completed' => \Morningtrain\NETSEasy\Events\WebhookPaymentRefundCompleted::class,
        'payment.refund.failed' => \Morningtrain\NETSEasy\Events\WebhookPaymentRefundFailed::class,
        'payment.refund.initiated.v2' => \Morningtrain\NETSEasy\Events\WebhookPaymentRefundInitiatedV2::class,
        'payment.reservation.created.v2' => \Morningtrain\NETSEasy\Events\WebhookPaymentReservationCreatedV2::class,
        'payment.reservation.failed' => \Morningtrain\NETSEasy\Events\WebhookPaymentReservationFailed::class,
    ],
    'in_test_mode' => env('NETS_EASY_IN_TEST_MODE', true),
    'secret_key' => env('NETS_EASY_SECRET_KEY'),
];
