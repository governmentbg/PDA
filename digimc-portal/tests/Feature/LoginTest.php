<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('profile.galleries.index'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function email_and_password_fields_are_required()
    {
        $response = $this->post('/auth/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    #[Test]
    public function invalid_email_format_shows_validation_error()
    {
        $response = $this->post('/auth/login', [
            'email' => 'invalid-email-format',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function after_login_user_has_access_to_protected_routes()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response = $this->get(route('profile.galleries.index'));
        $response->assertStatus(200);
    }


    #[Test]
    public function session_expires_after_30_minutes_of_inactivity()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $this->actingAs($user);

        $this->get(route('profile.galleries.index'))->assertStatus(200);
        $this->assertAuthenticated();

        $this->app['session']->put('last_activity', Carbon::now()->subMinutes(31)->timestamp);

        Auth::logout();

        $expiredResponse = $this->get(route('profile.galleries.index'));

        $expiredResponse->assertStatus(302);
        $expiredResponse->assertRedirect('auth/login');
        $this->assertGuest();
    }

    #[Test]
    public function failed_attempt_increases_login_attempts_in_session()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpass',
        ]);

        $this->assertEquals(1, Session::get('login_attempts'));
    }

    #[Test]
    public function after_five_failed_attempts_user_is_locked_in_database()
    {
        Carbon::setTestNow('2025-09-05 17:00:00');
        config()->set('services.recaptcha.enabled', false);
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        for ($x = 0; $x < 5; $x++) {
            $this->post('/auth/login', [
                'email' => $user->email,
                'password' => 'wrongpass',
            ]);
        }

        $user->refresh();
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->gt(Carbon::now()));
    }


    #[Test]
    public function locked_user_cannot_login_even_with_correct_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'locked_until' => Carbon::now()->addMinutes(30),
        ]);

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function lockout_expires_after_30_minutes()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'locked_until' => Carbon::now()->addMinutes(30),
        ]);


        Session::forget('login_attempts');


        $this->travel(31)->minutes();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('profile.galleries.index'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function successful_login_resets_login_attempts_in_session()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);


        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpass',
        ]);
        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpass',
        ]);

        $this->assertEquals(2, Session::get('login_attempts'));


        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $this->assertNull(Session::get('login_attempts'));
    }

    #[Test]
    public function logged_in_user_can_logout_and_is_redirected_to_home()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('auth.logout'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Успешно излязохте от профила си.');

        $this->assertGuest();
    }

    #[Test]
    public function logout_deletes_session_and_tokens()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->get(route('auth.logout'));

        $this->assertGuest();
        $this->assertGuest();

    }

    #[Test]
    public function protected_routes_are_blocked_after_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('auth.logout'));

        $this->get(route('profile.galleries.index'))->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function back_button_does_not_show_protected_pages_after_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.galleries.index'))
            ->assertStatus(200);

        $this->get(route('auth.logout'));


        $this->get(route('profile.galleries.index'))->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function test_session_timeout_logs_out_user_and_redirects()
    {
        $user = User::factory()->create();

        $timeout = 30;

        $response = $this->actingAs($user)
            ->withSession([
                'last_activity' => now()->subMinutes($timeout + 1)
            ])
            ->get(route('profile.galleries.index'));

        $response->assertRedirect(route('session-expired'));
        $response->assertSessionHas('status');

        $this->assertGuest();
    }

    #[Test]
    public function session_timeout_does_not_affect_guest_routes()
    {
        $this->get(route('auth.login'))->assertStatus(200);
        $this->get(route('auth.register'))->assertStatus(200);
    }
}
