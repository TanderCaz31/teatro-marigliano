<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class BookingService
{
    // Assigns the lowest free seat, excluding past or sold-out performances
    public function book(User $user, Performance $performance): Ticket
    {
        if ($performance->starts_at->isPast()) {
            throw ValidationException::withMessages(['ticket' => "L'esibizione selezionata è già incominciata."]);
        }

        $freeSeat = $this->firstFreeSeat($performance);

        if ($freeSeat === null) {
            throw ValidationException::withMessages(['ticket' => 'Sono esauriti i posti disponibili per questa esibizione.']);
        }

        return $user->tickets()->create([
            'performance_id' => $performance->id,
            'seat_number' => $freeSeat,
        ]);
    }

    // Goes through all the seats and returns the first available one
    private function firstFreeSeat(Performance $performance): ?int
    {
        $takenSeats = $performance->tickets()->pluck('seat_number');

        for ($seat = 1; $seat <= $performance->capacity; $seat++) {
            if (! $takenSeats->contains($seat)) {
                return $seat;
            }
        }

        return null;
    }
}
