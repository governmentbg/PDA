<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CulturalObject>
 */
class CulturalObjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifier' => $this->faker->name,
            'type' => $this->faker->randomElement(['image', 'video', 'audio', 'pdf', 'three_d']),
            'title' => $this->faker->sentence(nbWords: 5),
            'original_title' => $this->faker->sentence(nbWords: 10),
            'other_title' => $this->faker->sentence(nbWords: 10),
            'artist' => $this->faker->name,
            'description' => $this->faker->text(),
            'cultural_object_provided_by' => Provider::factory(),
            'creation_date' => $this->faker->date,
            'current_location' => $this->faker->name,
            'keywords' => $this->faker->name,
            'theme' => $this->faker->name,
            'subject_heading' => $this->faker->sentence(nbWords: 5),
            'geographic_heading' => $this->faker->sentence(nbWords: 5),
            'temporal_heading' => $this->faker->sentence(nbWords: 5),
            'language_code' => 'bg',
            'physical_dimensions' => $this->faker->name,
            'medium' => $this->faker->name,
            'previous_owner' => $this->faker->name,
            'acquisition' => $this->faker->name,
            'original_media' => $this->faker->name,
            'rights_holder' => $this->faker->name,
            'rights' => $this->faker->name,
            'contentdescription' => $this->faker->text,
            'amount' => $this->faker->numberBetween($min = 100, $max = 9000000),
            'currency' => $this->faker->currencyCode(),
            'thumbnail_url' => 'https://placehold.co/500x500',
            'extended_view_url' => 'https://placehold.co/500x500',
        ];
    }
}
