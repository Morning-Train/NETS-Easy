<?php

namespace Morningtrain\NETSEasy\DTOs;

use Morningtrain\NETSEasy\Contracts\PaymentHandleWebhook;

/**
 * @property-read Item[] $items
 */
readonly class Payment implements \JsonSerializable
{
    public function __construct(
        protected string $reference,
        protected array $items,
        protected string $currency,
        protected ?string $termsUrl = null,
        protected ?string $returnUrl = null,
        protected ?string $cancelUrl = null,
        protected ?Customer $customer = null,
        protected bool $autoCharge = false,
    ) {}

    public static function new(
        string $reference,
        array $items,
        string $currency,
        ?string $termsUrl = null,
        ?string $returnUrl = null,
        ?string $cancelUrl = null,
        ?Customer $customer = null,
        bool $autoCharge = false,
    ): self {
        return new static(
            reference: $reference,
            items: $items,
            currency: $currency,
            termsUrl: $termsUrl,
            returnUrl: $returnUrl,
            cancelUrl: $cancelUrl,
            customer: $customer,
            autoCharge: $autoCharge,
        );
    }

    public function jsonSerialize(): mixed
    {
        $webhooks = [];

        $paymentHandleWebhook = app(PaymentHandleWebhook::class);
        $webhookNames = config('nets-easy.events', []);

        foreach ($webhookNames as $webhookName => $webhookClass) {
            $webhooks[] = [
                'eventName' => $webhookName,
                'url' => $paymentHandleWebhook->url($webhookName),
                'authorization' => config('nets-easy.auth-key'),
            ];
        }

        return [
            'order' => array_filter([
                'items' => $this->items,
                'amount' => array_reduce($this->items, function ($carry, Item $item) {
                    return $carry + $this->convertToOneHundredthInt($item->getGrossTotalAmount());
                }, 0),
                'reference' => $this->reference,
                'currency' => $this->currency,
            ]),
            'checkout' => array_filter([
                'integrationType' => 'HostedPaymentPage',
                'returnUrl' => $this->returnUrl,
                'cancelUrl' => $this->cancelUrl,
                'termsUrl' => $this->termsUrl,
                'charge' => $this->autoCharge,
                'merchantHandlesConsumerData' => ! empty($this->customer),
                'consumer' => $this->customer,
            ]),
            'notifications' => [
                'webHooks' => $webhooks,
            ],
        ];
    }

    private function convertToOneHundredthInt(float $number): int
    {
        return (int) round($number * 100, 0);
    }
}
