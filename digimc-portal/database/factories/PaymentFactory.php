<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'payment_code' => strtoupper(Str::random(10)),
            'external_transaction_id' => strtoupper(Str::random(15)),
            'status' => $this->faker->randomElement(PaymentStatusEnum::ALL),
            'total_amount' => $this->faker->randomFloat(2, 2, 100),
            'expires_at' => now()->addDays(3),
            'paid_at' => now(),
        ];
    }

    public function paid()
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::PAID,
            'paid_at' => now(),
        ]);
    }
}
