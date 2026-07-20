<?php

namespace Tests\Feature;

use App\Mail\RegistrationSuccessMail;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_is_activated_when_valid_token_is_used(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'activation_token' => Str::random(32),
            'activation_token_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->get('/auth/activate/' . $user->activation_token);

        $response->assertRedirect(route('auth.login'));

        $user = $user->fresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->activation_token);
        $this->assertNull($user->activation_token_expires_at);
    }

    #[Test]
    public function expired_token_sends_new_activation_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'activation_token' => Str::random(32),
            'activation_token_expires_at' => now()->subMinutes(10),
        ]);

        $response = $this->get('/auth/activate/' . $user->activation_token);

        $response->assertRedirect(route('auth.register'));
        $response->assertSessionHas(
            'error',
            'Линкът за активация е изтекъл. Изпратихме ви нов имейл с активен линк.'
        );

        $user = $user->fresh();
        $this->assertNotNull($user->activation_token);
        $this->assertTrue($user->activation_token_expires_at->gt(now()));

        Mail::assertSent(ActivationMail::class, 1);
    }

    #[Test]
    public function invalid_token_shows_error(): void
    {
        $response = $this->get('/auth/activate/' . Str::random(32));

        $response->assertRedirect(route('auth.register'));
        $response->assertSessionHas('error', 'Невалиден линк за активация.');
    }

    #[Test]
    public function valid_activation_token_activates_user_and_sends_success_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'activation_token' => Str::random(32),
            'activation_token_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->get('/auth/activate/' . $user->activation_token);

        $response->assertRedirect(route('auth.login'));

        $user = $user->fresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->activation_token);
        $this->assertNull($user->activation_token_expires_at);

        Mail::assertSent(RegistrationSuccessMail::class, 1);

        Mail::assertSent(RegistrationSuccessMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
