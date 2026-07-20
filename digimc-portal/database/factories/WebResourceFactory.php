<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebResource>
 */
class WebResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifier' => $this->faker->sentence,
            'type' => $this->faker->word,
            'creator' => $this->faker->name,
            'description' => $this->faker->text,
            'format' => $this->faker->word,
            'rights_holder' => $this->faker->name,
            'resource_type' => $this->faker->mimeType(),
            'conforms_to' => $this->faker->word,
            'created_at' => $this->faker->date,
            'extent' => $this->faker->time('H:i:s'),
            'issued' => $this->faker->date,
            'web_resource_address' => $this->faker->url,
            'rights' => $this->faker->word,
            'sensitive_content' => $this->faker->boolean,
            'content_warning' => $this->faker->boolean,
            'warning_text' => $this->faker->sentence,
            'visualizationtype' => $this->faker->randomElement(['image', 'tiff', 'pdf', 'audio', 'video', '3d','misc']),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'paid_content' => $this->faker->randomElement(['yes', 'no']),
            'trailer_address' => $this->faker->url,
            'web_resource_address_download' => $this->faker->url,
            'mimetype_thumbnail' => $this->faker->mimeType(),
            'mimetype_trailer' => $this->faker->mimeType(),
            'mimetype_download' => $this->faker->mimeType(),
            'source' => $this->faker->company,
            'title' => $this->faker->sentence(3),
        ];
    }
}
