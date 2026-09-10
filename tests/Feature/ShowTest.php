<?php

namespace Tests\Feature;

use App\Models\Performance;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_browse_the_shows()
    {
        Show::factory()->count(3)->create();

        $this->get(route('shows.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Shows/Index')
                ->has('shows', 3)
                ->has('shows.0.performances_count')
            );
    }

    public function test_show_page_lists_performances()
    {
        $show = Show::factory()->create();
        Performance::factory()->count(2)->create(['show_id' => $show->id]);

        $this->get(route('shows.show', $show))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Shows/Show')
                ->where('show.id', $show->id)
                ->has('show.performances', 2)
            );
    }

    public function test_guests_cannot_reach_the_create_page()
    {
        $this->get(route('shows.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_cannot_store_a_show()
    {
        $this->post(route('shows.store'), [
            'title' => 'Titolo',
            'description' => 'Descrizione',
            'duration_minutes' => 100,
            'is_featured' => false,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('shows', 0);
    }

    public function test_auth_user_can_store_a_show()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('shows.store'), [
                'title' => 'Titolo',
                'description' => 'Descrizione',
                'duration_minutes' => 100,
                'is_featured' => true,
            ])->assertRedirect(route('shows.index'));

        $this->assertDatabaseHas('shows', [
            'title' => 'Titolo',
            'is_featured' => true,
        ]);
    }

    // --- Tests on working cases ---
    public function test_show_can_be_become_unfeatured()
    {
        $user = User::factory()->create();
        $show = Show::factory()->featured()->create();
        $payload = Show::factory()->make($show->attributesToArray())->toArray();
        $payload['is_featured'] = false;

        $this->actingAs($user)
            ->put(route('shows.update', $show), $payload)
            ->assertRedirect(route('shows.index'));

        $this->assertDatabaseHas('shows', ['id' => $show->id, 'is_featured' => false]);
    }

    public function test_show_can_be_deleted()
    {
        $user = User::factory()->create();
        $show = Show::factory()->create();

        $this->actingAs($user)
            ->delete(route('shows.destroy', $show))
            ->assertRedirect(route('shows.index'));

        $this->assertModelMissing($show);
    }

    // --- Tests on failing cases ---
    public static function showValProvider()
    {
        return [
            'title required' => ['title', ''],
            'title too long' => ['title', str_repeat('a', 256)],
            'description too long' => ['description', str_repeat('a', 1001)],
            'duration required' => ['duration_minutes', ''],
            'duration must be integer' => ['duration_minutes', 'due ore'],
            'duration cannot be zero' => ['duration_minutes', 0],
            'is_featured required' => ['is_featured', ''],
            'is_featured must be boolean' => ['is_featured', 'true'],
        ];
    }

    #[DataProvider('showValProvider')]
    public function test_show_store_val($field, $value)
    {
        $user = User::factory()->create();
        $payload = Show::factory()->make()->toArray();
        $payload[$field] = $value;

        $response = $this
            ->actingAs($user)
            ->post(route('shows.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors($field);
        $this->assertDatabaseCount('shows', 0);
    }

    #[DataProvider('showValProvider')]
    public function test_show_update_val($field, $value)
    {
        $user = User::factory()->create();
        $show = Show::factory()->create();
        $payload = Show::factory()->make($show->attributesToArray())->toArray();
        $payload[$field] = $value;

        $response = $this
            ->actingAs($user)
            ->put(route('shows.update', $show), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors($field);
        $this->assertDatabaseMissing('shows', [$field => $value]);
    }
}
