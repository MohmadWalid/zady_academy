<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_code' => 'PAY-' . now()->format('Ym') . '-' . $this->faker->unique()->numerify('####'),
            'subscription_id' => Subscription::factory(),
            'amount' => 500,
            'method' => 'cash',
            'status' => 'approved',
        ];
    }
}
