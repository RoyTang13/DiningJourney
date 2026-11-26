<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_profile_navigation_menu_is_hidden_for_guests_and_uses_the_user_photo(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('data-profile-menu')
            ->assertDontSee('>Profile</a>', false);

        $user = User::factory()->create(['name' => 'Profile Menu Tester']);
        $photoPath = "profile-photos/{$user->id}/avatar.jpg";
        $user->forceFill(['profile_photo_path' => $photoPath])->save();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('data-profile-menu')
            ->assertSee('profile-menu-name')
            ->assertSee('Profile Menu Tester')
            ->assertSee(Storage::disk('public')->url($photoPath), false)
            ->assertSee('My Profile')
            ->assertSee(route('favorites.index'), false)
            ->assertSee('My Favourites')
            ->assertSee('profile-menu-logout')
            ->assertSee('Logout');
    }

    public function test_user_can_upload_a_cropped_profile_photo_and_replace_their_old_one(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'name' => 'Photo Owner',
            'email' => 'photo-owner@example.test',
        ]);
        $oldPath = "profile-photos/{$user->id}/old.jpg";
        Storage::disk('public')->put($oldPath, 'previous photo');
        $user->forceFill(['profile_photo_path' => $oldPath])->save();

        $response = $this->actingAs($user)->postJson(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('profile.png', 512, 512),
        ])->assertOk()
            ->assertJsonStructure(['message', 'url']);

        $savedPath = $user->fresh()->profile_photo_path;
        $this->assertIsString($savedPath);
        $this->assertNotSame($oldPath, $savedPath);
        $this->assertStringStartsWith("profile-photos/{$user->id}/", $savedPath);
        Storage::disk('public')->assertExists($savedPath);
        Storage::disk('public')->assertMissing($oldPath);
        $this->assertSame('Photo Owner', $user->fresh()->name);
        $this->assertSame('photo-owner@example.test', $user->fresh()->email);
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url($savedPath), false);
    }

    public function test_profile_photo_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('profile.photo'), [
                'photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('photo');

        $this->assertNull($user->fresh()->profile_photo_path);
        $this->assertSame([], Storage::disk('public')->allFiles("profile-photos/{$user->id}"));
    }

    public function test_profile_photo_upload_rejects_files_over_the_size_limit(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('profile.photo'), [
                'photo' => UploadedFile::fake()->image('oversized.jpg', 512, 512)->size(6000),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('photo');

        $this->assertNull($user->fresh()->profile_photo_path);
    }

    public function test_profile_photo_upload_requires_authentication(): void
    {
        $this->postJson(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('profile.jpg', 512, 512),
        ])->assertUnauthorized();
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

    public function test_admin_routes_are_forbidden_for_non_administrators(): void
    {
        $user = User::factory()->create();

        $this->get('/admin')->assertRedirect(route('login'));
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/places/attraction')->assertForbidden();
    }

    public function test_admin_can_view_the_dashboard_and_management_screens(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Place::factory()->create(['type' => 'attraction', 'name' => 'Admin Test Attraction']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Admin Test Attraction');

        $this->get('/admin/users')->assertOk()->assertSee($admin->email);
        $this->get('/admin/places/attraction')->assertOk()->assertSee('Admin Test Attraction');
        $this->get('/admin/places/restaurant/create')->assertOk()->assertSee('Create listing');
    }

    public function test_admin_can_create_update_and_delete_a_place(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $data = [
            'name' => 'Garden Gallery',
            'slug' => 'garden-gallery',
            'category' => 'Nature & Parks',
            'area' => 'Kuala Lumpur',
            'address' => '10 Garden Road',
            'description' => 'A peaceful city garden.',
            'image_path' => '/images/kuala-lumpur-skyline.jpg',
            'rating' => 4.5,
            'review_count' => 20,
            'price_level' => 1,
            'tags' => 'nature, family',
            'latitude' => 3.15,
            'longitude' => 101.7,
        ];

        $this->post(route('admin.places.store', 'attraction'), $data)
            ->assertRedirect(route('admin.places.index', 'attraction'));

        $place = Place::where('slug', 'garden-gallery')->firstOrFail();
        $this->assertSame(['nature', 'family'], $place->tags);
        $this->assertDatabaseHas('places', ['id' => $place->id, 'type' => 'attraction']);

        $data['name'] = 'Garden Gallery Updated';
        $data['slug'] = 'garden-gallery-updated';
        $this->put(route('admin.places.update', ['type' => 'attraction', 'place' => $place->id]), $data)
            ->assertRedirect(route('admin.places.index', 'attraction'));

        $this->assertDatabaseHas('places', ['id' => $place->id, 'name' => 'Garden Gallery Updated']);
        $this->delete(route('admin.places.destroy', ['type' => 'attraction', 'place' => $place->id]))
            ->assertRedirect();
        $this->assertDatabaseMissing('places', ['id' => $place->id]);
    }

    public function test_admin_cannot_manage_a_place_under_the_wrong_type(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $place = Place::factory()->create(['type' => 'attraction']);

        $this->actingAs($admin)
            ->get(route('admin.places.edit', ['type' => 'restaurant', 'place' => $place->id]))
            ->assertNotFound();
    }

    public function test_admin_cannot_remove_their_own_or_last_admin_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $admin), ['is_admin' => '0'])
            ->assertSessionHasErrors('is_admin');

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_admin' => true]);
    }

    public function test_admin_can_manage_other_users_but_last_admin_is_protected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $member), ['is_admin' => '1'])
            ->assertRedirect()
            ->assertSessionHas('status');
        $this->assertTrue($member->fresh()->isAdmin());

        $this->delete(route('admin.users.destroy', $otherAdmin))
            ->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);

        $this->patch(route('admin.users.update', $member), ['is_admin' => '0'])
            ->assertRedirect();
        $this->assertSame(1, User::where('is_admin', true)->count());

        $this->patch(route('admin.users.update', $admin), ['is_admin' => '0'])
            ->assertSessionHasErrors('is_admin');
    }

    public function test_admin_can_be_promoted_from_the_command_line(): void
    {
        $user = User::factory()->create(['email' => 'new-admin@example.test']);

        $this->artisan('admin:promote', ['email' => $user->email])
            ->expectsOutputToContain('Administrator access granted')
            ->assertSuccessful();

        $this->assertTrue($user->fresh()->isAdmin());
    }
}
