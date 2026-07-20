<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Audio>
 */
class AudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'performer' => $this->faker()->name,
            'producer' => $this->faker()->name,
            'duration' => $this->faker()->randomDigit,
            'recording_team' => $this->faker()->name,
            'audio_original_title' => $this->faker()->name,
            'composer' => $this->faker()->name,
            'author_of_arrangement' => $this->faker()->name,
            'text_author' => $this->faker()->name,
            'editing_producer_name' => $this->faker()->name,
            'date_recorded' => $this->faker()->date,
            'broadcasting_date' => $this->faker()->date,
            'colutral_object_id' => $this->faker()->name,
            'sub_type' => $this->faker()->name,
            'interviewer' => $this->faker()->name,
            'interviewee' => $this->faker()->name,
        ];
    }
}
