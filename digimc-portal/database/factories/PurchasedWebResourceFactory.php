<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PurchasedWebResource;
use App\Models\User;
use App\Models\WebResource;
use App\Models\Payment;

class PurchasedWebResourceFactory extends Factory
{
    protected $model = PurchasedWebResource::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'web_resource_id' => WebResource::factory(),
            'payment_id' => Payment::factory(),
            'purchased_at' => now(),
        ];
    }
}
