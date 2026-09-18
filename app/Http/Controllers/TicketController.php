<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use App\Models\Ticket;
use App\Notifications\TicketBooked;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    // Displays the logged-in user's tickets, soonest performance first
    public function index(Request $request): Response
    {
        $tickets = $request->user()->tickets()
            ->with(['performance.show:id,title', 'performance.venue'])
            ->get()
            ->sortBy('performance.starts_at')
            ->values();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function store(Request $request, Performance $performance, BookingService $bookingService): RedirectResponse
    {
        // Delegate all the logic to the service
        $ticket = $bookingService->book($request->user(), $performance);
        $request->user()->notify(new TicketBooked($ticket));

        return to_route('tickets.index');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('delete', $ticket);
        $ticket->delete();

        return to_route('tickets.index');
    }
}
