<?php

namespace Morningtrain\NETSEasy\DTOs;

readonly class Address implements \JsonSerializable
{
    public function __construct(
        protected ?string $addressLine1 = null,
        protected ?string $addressLine2 = null,
        protected ?string $postalCode = null,
        protected ?string $city = null,
        protected ?string $country = null,
    ) {}

    public static function new(
        ?string $addressLine1 = null,
        ?string $addressLine2 = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $country = null,
    ): self {
        return new static(
            addressLine1: $addressLine1,
            addressLine2: $addressLine2,
            postalCode: $postalCode,
            city: $city,
            country: $country
        );
    }

    public function jsonSerialize(): mixed
    {
        return array_filter([
            'addressLine1' => $this->addressLine1,
            'addressLine2' => $this->addressLine2,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'country' => $this->country,
        ]);
    }
}
