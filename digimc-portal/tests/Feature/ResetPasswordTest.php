<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_reset_password_form()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->get(route('auth.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Нова парола');
    }

    #[Test]
    public function user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password123*',
            'password_confirmation' => 'Password123*',
        ]);

        $response->assertRedirect(route('auth.login'));
        $this->assertTrue(Hash::check('Password123*', $user->fresh()->password));
    }

    #[Test]
    public function user_cannot_reset_password_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->post(route('auth.password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'Password123*',
            'password_confirmation' => 'Password123*',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function password_must_be_at_least_8_characters_long()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Short1*',
            'password_confirmation' => 'Short1*',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_must_contain_at_least_one_uppercase_letter()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'lowercase123*',
            'password_confirmation' => 'lowercase123*',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_must_contain_at_least_one_lowercase_letter()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'UPPERCASE123*',
            'password_confirmation' => 'UPPERCASE123*',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_must_contain_at_least_one_number()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NoNumbers*',
            'password_confirmation' => 'NoNumbers*',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_must_contain_at_least_one_symbol()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NoSymbols123',
            'password_confirmation' => 'NoSymbols123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_must_be_confirmed()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password123*',
            'password_confirmation' => 'DifferentPassword123*',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function valid_password_is_accepted()
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password123*',
            'password_confirmation' => 'Password123*',
        ]);

        $response->assertRedirect(route('auth.login'));
        $this->assertTrue(Hash::check('Password123*', $user->fresh()->password));
    }

    #[Test]
    public function password_cannot_be_empty()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_cannot_be_null()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('auth.password.update'), [
            'token' => $token,
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors('password');
    }
}
