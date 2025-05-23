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
