<?php

namespace Morningtrain\NETSEasy\Dispatchers;

use Morningtrain\NETSEasy\Model\PaymentReference;

class PaymentEventDispatcher
{
    public static function dispatch(string $webhookName, ?PaymentReference $paymentReference): void
    {
        $webhookNames = config('nets-easy.events', []);

        if (! array_key_exists($webhookName, $webhookNames)) {
            return;
        }

        $eventClass = $webhookNames[$webhookName];

        if (! class_exists($eventClass) || ! method_exists($eventClass, 'dispatch')) {
            return;
        }

        dispatch(new $eventClass($paymentReference));
    }
}
