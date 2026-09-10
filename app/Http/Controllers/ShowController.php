<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShowRequest;
use App\Http\Requests\UpdateShowRequest;
use App\Models\Show;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ShowController extends Controller
{
    // Displays the page with all shows
    public function index()
    {
        return Inertia::render('Shows/Index', [
            'shows' => Show::withCount('performances')->orderBy('title')->get(),
        ]);
    }

    // Displays the info page for a single show
    public function show(Show $show)
    {
        return Inertia::render('Shows/Show', [
            'show' => $show->load(['performances' => fn ($query) => $query->with('venue')->orderBy('starts_at')]),
        ]);
    }

    // Displays the create page
    public function create()
    {
        Gate::authorize('create', Show::class);

        return Inertia::render('Shows/Create');
    }

    // Creates the model instance and redirects to index
    public function store(StoreShowRequest $request)
    {
        Show::create($request->validated());

        return to_route('shows.index');
    }

    // Displays the edit page for a show
    public function edit(Show $show)
    {
        Gate::authorize('update', $show);

        return Inertia::render('Shows/Edit', [
            'show' => $show,
        ]);
    }

    // Updates the model and redirects to index
    public function update(UpdateShowRequest $request, Show $show)
    {
        $show->update($request->validated());

        return to_route('shows.index');
    }

    // Deletes the model instance
    public function destroy(Show $show)
    {
        Gate::authorize('delete', $show);
        $show->delete();

        return to_route('shows.index');
    }
}
