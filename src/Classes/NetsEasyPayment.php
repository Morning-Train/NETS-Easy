<?php

namespace Morningtrain\NETSEasy\Classes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Morningtrain\NETSEasy\DTOs\Payment;
use Morningtrain\NETSEasy\Enums\PaymentStatus;
use Morningtrain\NETSEasy\Exceptions\PaymentNotFoundException;
use Morningtrain\NETSEasy\Model\PaymentReference;

class NetsEasyPayment
{
    public ?Payment $paymentDTO = null;

    protected array $netsEasyData = [];

    protected ?string $paymentId = null;

    protected ?string $paymentPageUrl = null;

    public function __construct(
        private readonly NetsEasyClient $netsEasyClient
    ) {}

    public static function new(): self
    {
        return app(NetsEasyPayment::class);
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function getPaymentPageUrl(?string $language = null): string
    {
        if (! $this->isCreated() || ! empty($this->paymentPageUrl)) {
            $url = $this->paymentPageUrl;
        } else {
            $url = $this->getNetsEasyDataByKey('payment.checkout.url', $this->paymentPageUrl);
        }

        if (! empty($language)) {
            if (str_contains($url, '?')) {
                $url .= '&language=' . $language;
            } else {
                $url .= '?language=' . $language;
            }
        }

        return $url;
    }

    public function setPaymentDTO(Payment $paymentDTO): self
    {
        $this->paymentDTO = $paymentDTO;

        return $this;
    }

    /**
     * @throws PaymentNotFoundException
     */
    public function setPaymentId(string $paymentId): self
    {
        if (PaymentReference::query()->where('payment_id', $paymentId)->doesntExist()) {
            throw new PaymentNotFoundException($paymentId);
        }

        $this->paymentId = $paymentId;

        $this->fetchDataFromPaymentId();

        return $this;
    }

    /**
     * @throws ConnectionException
     */
    public function create(): Response
    {
        $response = $this->netsEasyClient
            ->post('v1/payments', $this->paymentDTO);

        if ($response->status() === 201) {
            $body = json_decode($response->body());

            PaymentReference::query()
                ->updateOrCreate(
                    ['payment_id' => $body->paymentId],
                    ['status' => PaymentStatus::CREATED->value]
                );

            $this->paymentId = $body->paymentId;
            $this->paymentPageUrl = $body->hostedPaymentPageUrl;

            $this->fetchDataFromPaymentId();
        }

        return $response;
    }

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public function terminate(): Response
    {
        if (empty($this->paymentId)) {
            throw new \Exception('Payment id not found');
        }

        $response = $this->netsEasyClient
            ->put("v1/payments/{$this->paymentId}/terminate");

        if ($response->status() === 204) {
            $this->updatePaymentStatus(PaymentStatus::TERMINATED->value);

            $this->fetchDataFromPaymentId();
        }

        return $response;
    }

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public function cancel(): Response
    {
        if (empty($this->paymentId)) {
            throw new \Exception('Payment id not found');
        }

        $response = $this->netsEasyClient
            ->post("v1/payments/{$this->paymentId}/cancels");

        if ($response->status() === 204) {
            $this->updatePaymentStatus(PaymentStatus::CANCEL_CREATED->value);

            $this->fetchDataFromPaymentId();
        }

        return $response;
    }

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public function charge(): Response
    {
        if (empty($this->paymentId)) {
            throw new \Exception('Payment id not found');
        }

        if ($this->isCreated()) {
            $amount = $this->getNetsEasyDataByKey('payment.orderDetails.amount', 0);
        } else {
            $amount = $this->paymentDTO['order']['amount'] ?? 0;
        }

        $response = $this->netsEasyClient
            ->post(
                "v1/payments/{$this->paymentId}/charges",
                [
                    'amount' => (int) round($amount * 100, 0),
                ]
            );

        if ($response->status() === 201) {
            $this->updatePaymentStatus(PaymentStatus::CHARGE_CREATED->value);

            $this->fetchDataFromPaymentId();
        }

        return $response;
    }

    public function getPaymentInfoFromPaymentId(): ?array
    {
        if (empty($this->paymentId)) {
            return null;
        }

        try {
            $response = $this->netsEasyClient
                ->get('v1/payments/'.$this->paymentId);
        } catch (ConnectionException $e) {
            return null;
        }

        if ($response->status() === 200) {
            return json_decode($response->body(), true)['payment'] ?? [];
        }

        return null;
    }

    private function getNetsEasyDataByKey(string $key, mixed $default = null): mixed
    {
        if (empty($this->netsEasyData)) {
            return $default;
        }

        $value = $this->netsEasyData;
        $keyParts = explode('.', $key);

        foreach ($keyParts as $keyPart) {
            if (! isset($value->{$keyPart})) {
                return $default;
            }

            $value = $value->{$keyPart};
        }

        return $value;
    }

    private function isCreated(): bool
    {
        return PaymentReference::query()
            ->where('payment_id', $this->paymentId)
            ->whereNot('status', PaymentStatus::INITIATED->value)
            ->exists();
    }

    private function updatePaymentStatus(string $status): void
    {
        if (empty($this->paymentId)) {
            return;
        }

        PaymentReference::query()
            ->where('payment_id', $this->paymentId)
            ->update(['status' => $status]);
    }

    private function fetchDataFromPaymentId(): void
    {
        $paymentBody = $this->getPaymentInfoFromPaymentId();

        if (empty($paymentBody)) {
            return;
        }

        $this->netsEasyData = $paymentBody;
    }
}
