<?php

namespace Morningtrain\NETSEasy\DTOs;

readonly class Item implements \JsonSerializable
{
    public function __construct(
        protected string $reference,
        protected string $name,
        public int $unitPrice,
        protected string $unit,
        protected int $quantity = 1,
        protected int $taxRate = 0,
        protected bool $taxIncluded = false,
    ) {}

    public function getTaxAmount(): float
    {
        return $this->getNetTotalAmount() * $this->taxRate / 100;
    }

    public function getGrossTotalAmount(): float
    {
        return $this->getNetTotalAmount() + $this->getTaxAmount();
    }

    public function getNetTotalAmount(): float
    {
        return $this->getUnitPrice() * $this->quantity;
    }

    public function getUnitPrice(): float
    {
        if ($this->taxIncluded) {
            return (float) $this->unitPrice / (1 + ($this->taxRate / 100));
        }

        return $this->unitPrice;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'reference' => $this->reference,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'unitPrice' => $this->convertToOneHundredthInt($this->getUnitPrice()),
            'taxRate' => $this->convertToOneHundredthInt($this->taxRate),
            'taxAmount' => $this->convertToOneHundredthInt($this->getTaxAmount()),
            'grossTotalAmount' => $this->convertToOneHundredthInt($this->getGrossTotalAmount()),
            'netTotalAmount' => $this->convertToOneHundredthInt($this->getNetTotalAmount()),
        ];
    }

    private function convertToOneHundredthInt(float $number): int
    {
        return (int) round($number * 100, 0);
    }
}
