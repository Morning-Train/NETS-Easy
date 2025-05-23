<?php

namespace Morningtrain\NETSEasy\Helpers;

use Morningtrain\NETSEasy\Enums\PaymentStatus;
use Morningtrain\NETSEasy\Model\PaymentReference;

class NetsEasyPaymentStatus
{
    public function handleStatus(string $webhookName, PaymentReference $paymentReference): void
    {
        $webhookRequirements = $this->requirementsForWebhook($webhookName);

        if (empty($webhookRequirements)) {
            return;
        }

        if (in_array($paymentReference->status, $webhookRequirements['validStatuses'])) {
            $paymentReference->status = $webhookRequirements['newStatus'];
        }
    }

    private function requirementsForWebhook(string $webhookName): ?array
    {
        return match ($webhookName) {
            'payment.cancel.created' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                ],
                'newStatus' => PaymentStatus::CANCEL_CREATED->value,
            ],
            'payment.cancel.failed' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                    PaymentStatus::CANCEL_CREATED->value,
                ],
                'newStatus' => PaymentStatus::CANCEL_FAILED->value,
            ],
            'payment.charge.created.v2' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                ],
                'newStatus' => PaymentStatus::CHARGE_CREATED->value,
            ],
            'payment.charge.failed' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                ],
                'newStatus' => PaymentStatus::CHARGE_FAILED->value,
            ],
            'payment.checkout.completed' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                ],
                'newStatus' => PaymentStatus::CHECKOUT_COMPLETED->value,
            ],
            'payment.created' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                ],
                'newStatus' => PaymentStatus::CREATED->value,
            ],
            'payment.refund.completed' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                    PaymentStatus::PARTLY_CHARGE_CREATED->value,
                    PaymentStatus::CHARGE_CREATED->value,
                    PaymentStatus::REFUND_INITIATED->value,

                ],
                'newStatus' => PaymentStatus::REFUND_COMPLETED->value,
            ],
            'payment.refund.failed' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                    PaymentStatus::PARTLY_CHARGE_CREATED->value,
                    PaymentStatus::CHARGE_CREATED->value,
                    PaymentStatus::REFUND_COMPLETED->value,
                    PaymentStatus::REFUND_INITIATED->value,

                ],
                'newStatus' => PaymentStatus::REFUND_FAILED->value,
            ],
            'payment.refund.initiated.v2' => [
                'validStatuses' => [

                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                    PaymentStatus::PARTLY_CHARGE_CREATED->value,
                    PaymentStatus::CHARGE_CREATED->value,
                ],
                'newStatus' => PaymentStatus::REFUND_INITIATED->value,
            ],
            'payment.reservation.created.v2' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                ],
                'newStatus' => PaymentStatus::RESERVED->value,
            ],
            'payment.reservation.failed' => [
                'validStatuses' => [
                    PaymentStatus::INITIATED->value,
                    PaymentStatus::CREATED->value,
                    PaymentStatus::CHECKOUT_COMPLETED->value,
                    PaymentStatus::RESERVED->value,
                ],
                'newStatus' => PaymentStatus::RESERVE_FAILED->value,
            ],
            default => null,
        };
    }
}
