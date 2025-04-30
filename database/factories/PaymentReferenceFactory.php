<?php

namespace Morningtrain\NETSEasy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Morningtrain\NETSEasy\Model\PaymentReference;

class PaymentReferenceFactory extends Factory
{
    protected $model = PaymentReference::class;

    public function definition(): array
    {
        return [
            'payment_id' => $this->faker->unique()->word,
            'status' => $this->faker->numberBetween(0, 17),
            'webhook_ids' => json_encode([$this->faker->uuid]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
