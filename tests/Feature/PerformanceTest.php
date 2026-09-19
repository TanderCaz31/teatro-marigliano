<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Performance;
use App\Models\Show;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
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

    // - Not a test, but to avoid repeating the long payload each time -
    private function performancePayload(): array
    {
        return [
            'show_id' => Show::factory()->create()->id,
            'venue_id' => Venue::factory()->create()->id,
            'starts_at' => now()->addDays(5)->setTime(21, 0)->format('Y-m-d\TH:i'),
        ];
    }

    public function test_admin_can_create_a_performance(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $payload = $this->performancePayload(); // need to store this as the assertion checks if the data is the same

        $this->actingAs($admin)
            ->post(route('performances.store'), $payload)
            ->assertRedirect(route('performances.index'));

        $this->assertDatabaseHas('performances', [
            'show_id' => $payload['show_id'],
            'venue_id' => $payload['venue_id'],
        ]);
    }

    public function test_member_cannot_create_a_performance(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post(route('performances.store'), $this->performancePayload())
            ->assertForbidden();

        $this->assertDatabaseCount('performances', 0);
    }

    public function test_guest_cannot_create_a_performance(): void
    {
        $this->post(route('performances.store'), $this->performancePayload())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('performances', 0);
    }

    // - Tests on failing cases -
    // Same format as ShowTest.php
    public static function performanceValProvider(): array
    {
        return [
            'show required' => ['show_id', ''],
            'show must exist' => ['show_id', 9999],
            'venue required' => ['venue_id', ''],
            'venue must exist' => ['venue_id', 9999],
            'date required' => ['starts_at', ''],
            'date must be date format' => ['starts_at', 'domani sera'],
            'date in the past' => ['starts_at', '2020-01-01T21:00'],
        ];
    }

    #[DataProvider('performanceValProvider')]
    public function test_performance_store_val($field, $value): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $payload = $this->performancePayload();
        $payload[$field] = $value;

        $this->actingAs($admin)
            ->post(route('performances.store'), $payload)
            ->assertStatus(302)
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('performances', 0);
    }
}
