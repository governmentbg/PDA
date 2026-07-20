<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HasComponent>
 */
class HasComponentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cultural_object_id' => CulturalObject::factory(),
            'cultural_object_child_id' => CulturalObject::factory(),
        ];
    }
}
