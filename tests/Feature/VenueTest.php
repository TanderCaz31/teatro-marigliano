<?php

namespace Tests\Feature;

use App\Models\Performance;
use App\Models\Show;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class VenueTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_guests_can_browse(): void
    {
        $this->get(route('venues.index'))
            ->assertOk();
    }

    public function test_index_venue_without_performances_still_appear(): void
    {
        Venue::factory()->create();

        $this->get(route('venues.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('venues', 1)
                ->where('venues.0.upcoming_performances_count', 0)
                ->has('venues.0.upcoming_performances', 0)
            );
    }

    public function test_index_past_performances_are_excluded(): void
    {
        $venue = Venue::factory()->create();
        Performance::factory()->count(2)->create(['venue_id' => $venue->id]);
        Performance::factory()->past()->count(3)->create(['venue_id' => $venue->id]);

        $this->get(route('venues.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('venues.0.upcoming_performances_count', 2)
            );
    }

    public function test_index_each_performance_has_show_title(): void
    {
        $venue = Venue::factory()->create();
        $show = Show::factory()->create(['title' => 'Titolo']);
        Performance::factory()->create(['venue_id' => $venue->id, 'show_id' => $show->id]);

        $this->get(route('venues.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('venues.0.upcoming_performances.0.show.title', 'Titolo')
            );
    }
}
