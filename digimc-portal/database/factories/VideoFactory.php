<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'years_issued' => $this->faker->randomDigitNotZero(),
            'film_duration' => $this->faker->randomDigitNotZero(),
            'actor' => $this->faker->name,
            'filmmaker' => $this->faker->name,
            'scenario_writer' => $this->faker->name,
            'cameraman' => $this->faker->name,
            'producer' => $this->faker->name,
            'composer' => $this->faker->name,
            'mount' => $this->faker->name,
            'production_director' => $this->faker->name,
            'editor' => $this->faker->name,
            'other_related_persons' => $this->faker->name,
            'premiere' => $this->faker->date,
            'cultural_object_id' => CulturalObject::factory(),
            'sub_type' => $this->faker->name,
            'interviewer' => $this->faker->name,
            'interviewee' => $this->faker->name,
        ];
    }
}
