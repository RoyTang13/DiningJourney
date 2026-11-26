<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiningJourneyModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_places_from_the_database(): void
    {
        $place = Place::factory()->create([
            'type' => 'attraction',
            'name' => 'Petronas Twin Towers',
            'slug' => 'petronas-twin-towers',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee($place->name)
            ->assertSee('/images/kuala-lumpur-skyline.jpg');
    }

    public function test_search_filters_places_by_type_and_keyword(): void
    {
        Place::factory()->create(['type' => 'attraction', 'name' => 'Batu Caves']);
        Place::factory()->create(['type' => 'restaurant', 'name' => 'Batu Cafe']);

        $this->get('/search?type=attraction&q=Batu')
            ->assertRedirect(route('places.attractions', ['q' => 'Batu']));

        $this->get('/attractions?q=Batu')
            ->assertOk()
            ->assertSee('Batu Caves')
            ->assertDontSee('Batu Cafe');
    }

    public function test_user_can_register_and_save_preferences(): void
    {
        $this->post('/register', [
            'name' => 'Food Explorer',
            'email' => 'explorer@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('home'));

        $user = User::where('email', 'explorer@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);

        $this->put('/preferences', [
            'travel_interests' => ['culture', 'city views'],
            'dining_interests' => ['malaysian', 'halal'],
            'dining_location' => 'Bukit Bintang',
            'budget' => 2,
        ])->assertRedirect();

        $this->assertSame(['culture', 'city views'], $user->fresh()->travel_interests);
        $this->assertSame(['malaysian', 'halal'], $user->fresh()->dining_interests);
        $this->assertSame('Bukit Bintang', $user->fresh()->dining_location);
        $this->assertSame(2, $user->fresh()->budget);
    }

    public function test_recommendations_use_interests_and_budget(): void
    {
        $user = User::factory()->create([
            'dining_interests' => ['malaysian'],
            'budget' => 1,
        ]);
        Place::factory()->create([
            'type' => 'restaurant',
            'name' => 'Local Nasi Lemak',
            'tags' => ['malaysian'],
            'price_level' => 1,
        ]);
        Place::factory()->create([
            'type' => 'restaurant',
            'name' => 'Premium Dining',
            'tags' => ['malaysian'],
            'price_level' => 4,
        ]);

        $this->actingAs($user)
            ->get('/recommendations?type=restaurant')
            ->assertOk()
            ->assertSee('Local Nasi Lemak')
            ->assertDontSee('Premium Dining');
    }

    public function test_signed_in_user_can_save_and_remove_a_favorite(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();

        $this->actingAs($user)
            ->post(route('favorites.toggle', $place))
            ->assertRedirect();
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'place_id' => $place->id]);

        $this->post(route('favorites.toggle', $place))->assertRedirect();
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'place_id' => $place->id]);
    }

    public function test_account_pages_require_authentication(): void
    {
        $this->get('/profile')->assertRedirect(route('login'));
        $this->get('/favorites')->assertRedirect(route('login'));
        $this->get('/preferences')->assertRedirect(route('login'));
    }

    public function test_restaurants_can_be_compared_side_by_side(): void
    {
        $first = Place::factory()->create(['type' => 'restaurant', 'name' => 'First Restaurant']);
        $second = Place::factory()->create(['type' => 'restaurant', 'name' => 'Second Restaurant']);

        $this->get(route('places.compare', ['places' => [$first->id, $second->id]]))
            ->assertOk()
            ->assertSee($first->name)
            ->assertSee($second->name);
    }

    public function test_signed_in_user_can_change_their_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->actingAs($user)
            ->put('/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect();

        $this->assertTrue(password_verify('new-password', $user->fresh()->password));
    }
}
