<?php

namespace Database\Factories;

use App\Models\Performance;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $performance = Performance::inRandomOrder()->first() ?? Performance::factory()->create();

        do {
            $seat = random_int(1, $performance->capacity);
        } while (Ticket::where('performance_id', $performance->id)
            ->where('seat_number', $seat)
            ->exists());

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'performance_id' => $performance->id,
            'seat_number' => $seat,
        ];
    }
}
