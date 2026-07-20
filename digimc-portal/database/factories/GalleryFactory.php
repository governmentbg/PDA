<?php

namespace Database\Factories;

use App\Enums\GalleryEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gallery>
 */
class GalleryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'user_id' => User::factory(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([GalleryEnum::STATUS_PRIVATE, GalleryEnum::STATUS_PUBLIC]),
        ];
    }
}
