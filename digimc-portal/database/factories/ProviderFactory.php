<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifier' => $this->faker->uuid,
            'type' => $this->faker->word,
            'description' => $this->faker->text,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'email' => $this->faker->email,
            'website' => $this->faker->url,
            'territory' => $this->faker->word,
            'contact_person' => $this->faker->name,
            'title' => $this->faker->name,
        ];
    }
}
