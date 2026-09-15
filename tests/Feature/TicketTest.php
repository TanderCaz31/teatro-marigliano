<?php

namespace Tests\Feature;

use App\Models\Performance;
use App\Models\Show;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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

    public function test_seat_code_is_calculated_from_the_venue_layout(): void
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create(['total_seats' => 72, 'rows' => 12]);
        $performance = Performance::factory()->create(['venue_id' => $venue->id]);

        $firstSeat = Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id, 'seat_number' => 1]); // A1
        $secondRow = Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id, 'seat_number' => 7]); // B1
        $lastSeat = Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id, 'seat_number' => 72]); // L6

        $this->assertSame('A1', $firstSeat->seat_code);
        $this->assertSame('B1', $secondRow->seat_code);
        $this->assertSame('L6', $lastSeat->seat_code);
    }

    // - Tickets index page -
    public function test_guests_cannot_see_the_tickets_page(): void
    {
        $this->get(route('tickets.index'))
            ->assertRedirect(route('login'));
    }

    public function test_tickets_page_shows_only_own_tickets(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $perf1 = Performance::factory()->create();
        $perf2 = Performance::factory()->create();

        Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $perf1->id]);
        Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $perf2->id]);
        Ticket::factory()->create(['user_id' => $user2->id, 'performance_id' => $perf2->id]);

        $this->actingAs($user)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tickets/Index')
                ->has('tickets', 2)
            );
    }

    // - BookingService & Booking logic -
    public function test_guests_cannot_book(): void
    {
        $performance = Performance::factory()->create();

        $this->post(route('tickets.store', $performance))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_booking_assigns_the_lowest_free_seat(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $performance = Performance::factory()->create();
        Ticket::factory()->create(['user_id' => $user2->id, 'performance_id' => $performance->id, 'seat_number' => 1]);
        Ticket::factory()->create(['user_id' => $user2->id, 'performance_id' => $performance->id, 'seat_number' => 3]);

        $this->actingAs($user)
            ->post(route('tickets.store', $performance))
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'performance_id' => $performance->id,
            'seat_number' => 2,
        ]);
    }

    public function test_cannot_book_sold_out_performance(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $performance = Performance::factory()->create(['capacity' => 1]);
        Ticket::factory()->create(['user_id' => $user2->id, 'performance_id' => $performance->id]);

        $this->actingAs($user)
            ->post(route('tickets.store', $performance))
            ->assertSessionHasErrors('ticket');

        $this->assertDatabaseCount('tickets', 1);
    }

    public function test_past_performance_cannot_be_booked(): void
    {
        $user = User::factory()->create();
        $performance = Performance::factory()->past()->create();

        $this->actingAs($user)
            ->post(route('tickets.store', $performance))
            ->assertSessionHasErrors('ticket');

        $this->assertDatabaseCount('tickets', 0);
    }

    // - Cancellation -
    public function test_user_can_cancel_their_upcoming_ticket(): void
    {
        $user = User::factory()->create();
        $performance = Performance::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id]);

        $this->actingAs($user)
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('tickets.index'));

        $this->assertModelMissing($ticket);
    }

    public function test_user_cannot_cancel_someone_elses_ticket(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $performance = Performance::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id]);

        $this->actingAs($user2)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->assertModelExists($ticket);
    }

    public function test_ticket_for_a_past_performance_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();
        $performance = Performance::factory()->past()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id]);

        $this->actingAs($user)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->assertModelExists($ticket);
    }
}
