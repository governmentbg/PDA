<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = ['sqlite', 'secondary'];

    #[Test]
    public function index_redirects_to_login_if_user_not_authenticated()
    {
        $response = $this->get(route('profile.favorites.index'));
        $response->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function index_returns_view_with_favorites_for_authenticated_user()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->get(route('profile.favorites.index'));

        $response->assertOk();
        $response->assertViewHas('paginatedFavoriteObjects');
    }

    #[Test]
    public function add_multiple_requires_authentication()
    {
        $response = $this->postJson(route('profile.favorites.add-multiple'), [
            'object_ids' => [1,2,3]
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function add_multiple_validates_object_ids_required()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->postJson(route('profile.favorites.add-multiple'), [
            'object_ids' => []
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('object_ids');
    }

    #[Test]
    public function remove_multiple_requires_authentication()
    {
        $response = $this->postJson(route('profile.favorites.remove-multiple'), [
            'object_ids' => [1,2]
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function remove_multiple_validates_object_ids_required()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->postJson(route('profile.favorites.remove-multiple'), [
            'object_ids' => []
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('object_ids');
    }

    #[Test]
    public function add_multiple_returns_json_success_response()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->postJson(route('profile.favorites.add-multiple'), [
            'object_ids' => [1,2,3]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'object_ids'
        ]);
    }

    #[Test]
    public function remove_multiple_returns_json_success_response()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->postJson(route('profile.favorites.remove-multiple'), [
            'object_ids' => [1,2]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'object_ids'
        ]);
    }
}
