<?php

namespace Morningtrain\NETSEasy\DTOs;

readonly class Customer implements \JsonSerializable
{
    public function __construct(
        protected ?string $reference = null,
        protected ?string $email = null,
        protected ?string $firstName = null,
        protected ?string $lastName = null,
        protected ?string $companyName = null,
        protected ?string $phoneNumber = null,
        protected ?string $phoneCountryPrefix = null,
        protected ?Address $billingAddress = null,
        protected ?Address $shippingAddress = null,
    ) {}

    public static function new(
        ?string $reference = null,
        ?string $email = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $companyName = null,
        ?string $phoneNumber = null,
        ?string $phoneCountryPrefix = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
    ): self {
        return new static(
            reference: $reference,
            email: $email,
            firstName: $firstName,
            lastName: $lastName,
            companyName: $companyName,
            phoneNumber: $phoneNumber,
            phoneCountryPrefix: $phoneCountryPrefix,
            billingAddress: $billingAddress,
            shippingAddress: $shippingAddress
        );
    }

    public function jsonSerialize(): mixed
    {
        $name = $this->getName();
        $customer = array_filter([
            'reference' => $this->reference,
            'email' => $this->email,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
        ]);

        if (! empty($this->phoneNumber) && ! empty($this->phoneCountryPrefix)) {
            $customer['phoneNumber'] = [
                'number' => $this->phoneNumber,
                'prefix' => $this->phoneCountryPrefix,
            ];
        }

        if (! empty($this->companyName)) {
            $customer['company'] = [
                'name' => $this->companyName,
            ];

            if (! empty($name)) {
                $customer['company']['contact'] = $name;
            }
        } elseif (! empty($name)) {
            $customer['privatePerson'] = $name;
        }

        return $customer;
    }

    private function getName(): ?array
    {
        $name = [];

        if (! empty($this->firstName)) {
            $name['firstName'] = $this->firstName;
        }

        if (! empty($this->lastName)) {
            $name['lastName'] = $this->lastName;
        }

        if (empty($name)) {
            return null;
        }

        return $name;
    }
}
