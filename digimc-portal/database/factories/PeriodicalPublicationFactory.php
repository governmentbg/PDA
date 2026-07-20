<?php

namespace Database\Factories;

use App\Models\TextObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriodicalPublication>
 */
class PeriodicalPublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periodical_publication_type' => $this->faker->word,
            'text_object_id' => TextObject::factory(),
        ];
    }
}
