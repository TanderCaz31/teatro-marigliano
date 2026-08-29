<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
{
    public const VENUENAMES = [
        'Sala Galileo',
        'Sala Dante',
        'Sala da Vinci',
        'Sala Leopardi'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(self::VENUENAMES),
            'total_seats' => fake()->numberBetween(3, 5)*24, //72, 96, 120
            'rows' => fake()->numberBetween(1, 3)*4, // 4, 8, 12
        ];
    }
}
