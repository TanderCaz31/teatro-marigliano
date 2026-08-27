<?php

namespace Database\Factories;

use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Show>
 */
class ShowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word().' '.fake()->word(),
            'description' => fake()->text(200),
            'duration_minutes' => fake()->numberBetween(100, 200),
            'is_featured' => false,
        ];
    }

    public function featured(): Factory
    {
        return $this->state(function () {
            return [
                'is_featured' => true,
            ];
        });
    }
}
