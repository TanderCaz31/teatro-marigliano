<?php

namespace Tests\Feature;

use App\Models\Performance;
use App\Models\Show;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_only_upcoming_performances_by_default(): void
    {
        $show = Show::factory()->create();
        $upcoming = Performance::factory()->create(['show_id' => $show->id]);
        Performance::factory()->past()->create(['show_id' => $show->id]);

        $this->get(route('performances.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Performances/Index')
                ->has('performances', 1)
                ->where('performances.0.id', $upcoming->id)
            );
    }

    public function test_index_search_filters_by_show_title(): void
    {
        $show1 = Show::factory()->create(['title' => 'Titolo1']);
        $show2 = Show::factory()->create(['title' => 'Titolo2']);
        $perf1 = Performance::factory()->create(['show_id' => $show1->id]);
        Performance::factory()->create(['show_id' => $show2->id]);

        $this->get(route('performances.index', ['search' => 'olo1']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Performances/Index')
                ->has('performances', 1)
                ->where('performances.0.id', $perf1->id)
            );
    }

    public function test_index_past_toggle_shows_only_past_performances(): void
    {
        $show = Show::factory()->create();
        Performance::factory()->create(['show_id' => $show->id]);
        $past = Performance::factory()->past()->create(['show_id' => $show->id]);

        $this->get(route('performances.index', ['past' => 1]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Performances/Index')
                ->has('performances', 1)
                ->where('performances.0.id', $past->id)
            );
    }

    public function test_index_featured_performances_first(): void
    {
        $regular = Show::factory()->create();
        $featured = Show::factory()->featured()->create();
        Performance::factory()->create(['show_id' => $regular->id, 'starts_at' => now()->addDay()]);
        Performance::factory()->create(['show_id' => $featured->id, 'starts_at' => now()->addDays(100)]);

        $this->get(route('performances.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('performances.0.show.id', $featured->id)
                ->where('performances.1.show.id', $regular->id)
            );
    }

    public function test_index_includes_the_number_of_tickets_sold(): void
    {
        $user = User::factory()->create();
        $performance = Performance::factory()->create();
        Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id, 'seat_number' => 1]);
        Ticket::factory()->create(['user_id' => $user->id, 'performance_id' => $performance->id, 'seat_number' => 2]);

        $this->get(route('performances.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('performances.0.tickets_count', 2)
            );
    }
}
