<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use App\Models\Show;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PerformanceController extends Controller
{
    // Displays performances using the user-selected sorts
    // Featured performances always first
    public function index(Request $request): Response
    {
        $filters = [
            'past' => $request->boolean('past'),
            'search' => $request->string('search'),
            'min_duration' => $request->integer('min_duration', 40),
            'max_duration' => $request->integer('max_duration', 240),
        ];

        $performances = Performance::with(['show:id,title,duration_mintues,is_featured', 'venue:id,name'])
            ->where('starts_at', $filters['past'] ? '<' : '>=', now())
            ->whereHas('show', fn ($query) => $query
                ->where('title', 'like', '%'.$filters['search'].'%')
                ->whereBetween('duration_minutes', [$filters['min_duration'], $filters['max_duration']]))

            ->orderByDesc(Show::select('is_featured')->whereColumn('shows.id', 'performances.show_id')) // Small subquery
            ->orderBy('starts_at', $filters['past'] ? 'desc' : 'asc')
            ->get();
        return Inertia::render('Performances/Index', [
            'performances' => $performances,
            'filters' => $filters
        ]);
    }
}
