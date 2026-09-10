<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Inertia\Inertia;

class VenueController extends Controller
{
    // Displays the page with all venues
    public function index()
    {
        return Inertia::render('Venues/Index', [
            'venues' => Venue::withCount('upcomingPerformances')
                ->with(['upcomingPerformances' => fn ($query) => $query->with('show:id,title')->limit(3)])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
