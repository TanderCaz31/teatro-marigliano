<?php

namespace Tests\Feature;

use App\Models\Performance;
use App\Models\Show;
use App\Models\Ticket;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_has_access_to_venue_and_performance(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(Performance::class, $ticket->performance);
        $this->assertInstanceOf(Show::class, $ticket->performance->show);
        $this->assertInstanceOf(Venue::class, $ticket->performance->venue);
    }
}
