<?php

namespace Database\Factories;

use App\Models\Performance;
use App\Models\Show;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Performance>
 */
class PerformanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $venue = Venue::inRandomOrder()->first() ?? Venue::factory()->create();
        return [
            'venue_id' => $venue->id,
            'show_id' => Show::inRandomOrder()->value('id') ?? Show::factory(),
            'starts_at' => now()->addDays(random_int(3, 14))->setTime(random_int(18, 21), 0),
            'capacity' => $venue->total_seats,
        ];
    }

    public function past(): Factory
    {
        return $this->state(function () {
            return [
                'starts_at' => now()->subDays(random_int(3, 14))->setTime(random_int(18, 21), 0),
            ];
        });
    }
}
