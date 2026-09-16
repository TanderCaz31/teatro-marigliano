<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
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

    public function test_guests_can_browse_the_shows(): void
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

    public function test_show_page_lists_performances(): void
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

    public function test_guests_cannot_reach_the_create_page(): void
    {
        $this->get(route('shows.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guests_cannot_store_a_show(): void
    {
        $this->post(route('shows.store'), [
            'title' => 'Titolo',
            'description' => 'Descrizione',
            'duration_minutes' => 100,
            'is_featured' => false,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('shows', 0);
    }

    public function test_admin_can_store_a_show(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::ADMIN]);

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

    // - Tests on working cases -
    public function test_show_can_be_become_unfeatured(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $show = Show::factory()->featured()->create();
        $payload = Show::factory()->make($show->attributesToArray())->toArray();
        $payload['is_featured'] = false;

        $this->actingAs($admin)
            ->put(route('shows.update', $show), $payload)
            ->assertRedirect(route('shows.index'));

        $this->assertDatabaseHas('shows', ['id' => $show->id, 'is_featured' => false]);
    }

    public function test_show_can_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $show = Show::factory()->create();

        $this->actingAs($admin)
            ->delete(route('shows.destroy', $show))
            ->assertRedirect(route('shows.index'));

        $this->assertModelMissing($show);
    }

    // - Tests on failing cases -
    public static function showValProvider(): array
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
    public function test_show_store_val($field, $value): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $payload = Show::factory()->make()->toArray();
        $payload[$field] = $value;

        $response = $this
            ->actingAs($admin)
            ->post(route('shows.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors($field);
        $this->assertDatabaseCount('shows', 0);
    }

    #[DataProvider('showValProvider')]
    public function test_show_update_val($field, $value): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $show = Show::factory()->create();
        $payload = Show::factory()->make($show->attributesToArray())->toArray();
        $payload[$field] = $value;

        $response = $this
            ->actingAs($admin)
            ->put(route('shows.update', $show), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors($field);
        $this->assertDatabaseMissing('shows', [$field => $value]);
    }

    // - Authorization -
    public static function pageAccessProvider(): array
    {
        return [
            'admin' => ['role' => RoleEnum::ADMIN, 'expectedStatus' => 200],
            'member' => ['role' => RoleEnum::MEMBER, 'expectedStatus' => 403],
        ];
    }

    public static function pageAccessProviderRedirect(): array
    {
        return [
            'admin' => ['role' => RoleEnum::ADMIN, 'expectedStatus' => 302, 'shouldSucceed' => true],
            'member' => ['role' => RoleEnum::MEMBER, 'expectedStatus' => 403, 'shouldSucceed' => false],
        ];
    }

    #[DataProvider('pageAccessProvider')]
    public function test_show_create_page_access(RoleEnum $role, int $expectedStatus): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('shows.create'))
            ->assertStatus($expectedStatus);
    }

    #[DataProvider('pageAccessProvider')]
    public function test_show_edit_page_access(RoleEnum $role, int $expectedStatus): void
    {
        $user = User::factory()->create(['role' => $role]);
        $show = Show::factory()->create();

        $this->actingAs($user)
            ->get(route('shows.edit', $show))
            ->assertStatus($expectedStatus);
    }

    #[DataProvider('pageAccessProviderRedirect')]
    public function test_show_store_route_access(RoleEnum $role, int $expectedStatus, bool $shouldSucceed): void
    {
        $user = User::factory()->create(['role' => $role]);
        $payload = Show::factory()->make()->toArray();

        $response = $this
            ->actingAs($user)
            ->post(route('shows.store'), $payload);

        $response->assertStatus($expectedStatus);
        $shouldSucceed
            ? $this->assertDatabaseHas('shows', ['title' => $payload['title']])
            : $this->assertDatabaseCount('shows', 0);
    }

    #[DataProvider('pageAccessProviderRedirect')]
    public function test_show_update_route_access(RoleEnum $role, int $expectedStatus, bool $shouldSucceed): void
    {
        $user = User::factory()->create(['role' => $role]);
        $show = Show::factory()->create();
        $payload = Show::factory()->make($show->attributesToArray())->toArray();
        $payload['title'] = 'Titolo 2';

        $response = $this
            ->actingAs($user)
            ->put(route('shows.update', $show), $payload);

        $response->assertStatus($expectedStatus);
        $shouldSucceed
            ? $this->assertDatabaseHas('shows', ['id' => $show->id, 'title' => 'Titolo 2'])
            : $this->assertDatabaseMissing('shows', ['title' => 'Titolo 2']);
    }

    #[DataProvider('pageAccessProviderRedirect')]
    public function test_show_destroy_route_access(RoleEnum $role, int $expectedStatus, bool $shouldSucceed): void
    {
        $user = User::factory()->create(['role' => $role]);
        $show = Show::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('shows.destroy', $show));

        $response->assertStatus($expectedStatus);
        $shouldSucceed
            ? $this->assertModelMissing($show)
            : $this->assertModelExists($show);
    }
}
