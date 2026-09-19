<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerformanceRequest;
use App\Models\Performance;
use App\Models\Show;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PerformanceController extends Controller
{
    // Displays performances using the user-selected filters
    // Featured performances always first
    public function index(Request $request): Response
    {
        $filters = [
            'past' => $request->boolean('past'),
            'search' => $request->string('search'),
            'min_duration' => $request->integer('min_duration', 40),
            'max_duration' => $request->integer('max_duration', 240),
        ];

        $performances = Performance::with(['show:id,title,duration_minutes,is_featured', 'venue:id,name'])
            ->withCount('tickets')
            ->where('starts_at', $filters['past'] ? '<' : '>=', now())
            ->whereHas('show', fn ($query) => $query
                ->where('title', 'like', '%'.$filters['search'].'%')
                ->whereBetween('duration_minutes', [$filters['min_duration'], $filters['max_duration']]))

            ->orderByDesc(Show::select('is_featured')->whereColumn('shows.id', 'performances.show_id')) // Small subquery
            ->orderBy('starts_at', $filters['past'] ? 'desc' : 'asc')
            ->get();

        return Inertia::render('Performances/Index', [
            'performances' => $performances,
            'filters' => $filters,
        ]);
    }

    // Loads the creation page
    public function create(): Response
    {
        Gate::authorize('create', Performance::class);

        return Inertia::render('Performances/Create', [
            'shows' => Show::orderBy('title')->get(['id', 'title', 'is_featured']),
            'venues' => Venue::orderBy('name')->get(['id', 'name', 'total_seats']),
        ]);
    }

    public function store(StorePerformanceRequest $request): RedirectResponse
    {
        $venue = Venue::find($request->integer('venue_id'));

        // capacity is not chosen by the admin, but by the venue they selected
        Performance::create([
            ...$request->validated(),
            'capacity' => $venue->total_seats,
        ]);

        return to_route('performances.index');
    }
}
