<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CulturalObjectLike>
 */
class CulturalObjectLikeFactory extends Factory
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
            'user_id' => User::factory(),
        ];
    }
}
