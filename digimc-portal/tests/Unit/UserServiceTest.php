<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_user_and_send_activation_email(): void
    {
        Mail::fake();

        $data = [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => 'password123',
        ];

        $service = new UserService();
        $user = $service->createUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['first_name'], $user->first_name);
        $this->assertEquals($data['last_name'], $user->last_name);
        $this->assertTrue(Hash::check($data['password'], $user->password));
        $this->assertNotNull($user->activation_token);
        $this->assertNotNull($user->activation_token_expires_at);

        Mail::assertSent(ActivationMail::class, fn($mail) => $mail->hasTo($user->email));
    }

    #[Test]
    public function it_can_resend_activation_email_to_inactive_user(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'activation_token' => 'oldtoken',
            'activation_token_expires_at' => now()->subHour(),
        ]);

        $service = new UserService();
        $result = $service->resendActivationEmail($user->email);

        $this->assertTrue($result);

        $user->refresh();
        $this->assertNotEquals('oldtoken', $user->activation_token);
        $this->assertTrue($user->activation_token_expires_at->gt(now()));

        Mail::assertSent(ActivationMail::class, fn($mail) => $mail->hasTo($user->email));
    }

    #[Test]
    public function resend_activation_email_returns_false_for_active_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $service = new UserService();
        $result = $service->resendActivationEmail($user->email);

        $this->assertFalse($result);
    }
}
