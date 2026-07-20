<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creation_date' => $this->faker->date,
            'publisher' => $this->faker->name,
            'issuer_person' => $this->faker->name,
            'issuing_year' => $this->faker->year,
            'cultural_object_id' => CulturalObject::factory(),
            'sub_type' => $this->faker->name,
        ];
    }
}
