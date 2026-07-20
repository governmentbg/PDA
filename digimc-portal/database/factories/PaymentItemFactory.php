<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentItem;
use App\Models\Payment;
use App\Models\WebResource;

class PaymentItemFactory extends Factory
{
    protected $model = PaymentItem::class;

    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'web_resource_id' => WebResource::factory(),
            'price' => $this->faker->randomFloat(2, 2, 50),
        ];
    }
}
