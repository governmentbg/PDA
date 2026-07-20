<?php

namespace Database\Factories;

use App\Models\CulturalObject;
use App\Models\WebResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HasWebViewExtended>
 */
class HasWebViewExtendedFactory extends Factory
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
            'web_resource_id' => WebResource::factory(),
        ];
    }
}
