<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\CulturalObject;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryCulturalObjectFactory extends Factory
{
    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GalleryCulturalObject>
     */
    public function definition()
    {
        return [
            'gallery_id' => Gallery::factory(),
            'cultural_object_id' => CulturalObject::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
