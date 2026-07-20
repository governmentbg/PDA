<?php

namespace Database\Factories;

use App\Enums\ArticleEnum;
use App\Models\ArticleType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;

        return [
            'published_at' => $this->faker->randomElement([$this->faker->date(), null], 1),
            'article_type_id' => ArticleType::factory(),
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'content' => $this->faker->text(maxNbChars: 1000),
            'status' => $this->faker->randomElement([ArticleEnum::STATUS_DRAFT, ArticleEnum::STATUS_PUBLISHED]),
        ];
    }
}
