<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'performance_id',
        'seat_number',
    ];

    protected $appends = ['seat_code'];

    public function performance(): BelongsTo
    {
        return $this->belongsTo(Performance::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function seatCode(): Attribute
    {
        return Attribute::make(
            get: function () {
                $venue = $this->performance->venue;
                $seatsPerRow = intdiv($venue->total_seats, $venue->rows); // As per the factory, results here will never have a remainder

                $position = $this->seat_number - 1;
                $rowLetter = range('A', 'Z')[intdiv($position, $seatsPerRow)];
                $seatInRow = $position % $seatsPerRow + 1;

                return $rowLetter.$seatInRow;
            },
        );
    }
}
