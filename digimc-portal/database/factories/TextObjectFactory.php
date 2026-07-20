<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TextObject>
 */
class TextObjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year_of_publication' => $this->faker->year,
            'date_of_publication' => $this->faker->date,
            'translator' => $this->faker->name,
            'writer' => $this->faker->name,
            'sponsor' => $this->faker->name,
            'issuer_person' => $this->faker->name,
            'publisher' => $this->faker->name,
            'first_аuthor' => $this->faker->name,
            'cultural_object_id' => CulturalObject::factory(),
            'sub_type' => $this->faker->name,
            'issuing_institution' => $this->faker->name,
        ];
    }
}
