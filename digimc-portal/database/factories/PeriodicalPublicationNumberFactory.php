<?php

namespace Database\Factories;

use App\Models\PeriodicalPublication;
use App\Models\TextObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriodicalPublicationNumber>
 */
class PeriodicalPublicationNumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periodical_publication_issue_number' => $this->faker->numberBetween(10000,999999999),
            'periodical_publication_issue_year' => $this->faker->year,
            'periodical_publication_issue_month' => $this->faker->month,
            'periodical_publication_issue_day' => $this->faker->dayOfMonth,
            'text_object_id' => TextObject::factory(),
            'periodical_publication_id' => PeriodicalPublication::factory(),
        ];
    }
}
