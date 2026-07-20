<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThreeD>
 */
class ThreeDFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'publisher' => $this->faker->name,
            'cultural_object_id' => CulturalObject::factory(),
            'sub_type' => $this->faker->name,
            'design_house' => $this->faker->name,
        ];
    }
}
