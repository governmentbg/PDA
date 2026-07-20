<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_forgot_password_form()
    {
        $response = $this->get(route('auth.password.request'));
        $response->assertStatus(200);
        $response->assertSee('Забравена парола');
    }

    #[Test]
    public function guest_can_request_password_reset_link()
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->post(route('auth.password.email'), [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status');

        Mail::assertSent(function (Mailable $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    #[Test]
    public function requesting_reset_for_unknown_email_still_shows_success()
    {
        $response = $this->post(route('auth.password.email'), [
            'email' => 'unknown@example.com',
        ]);

        $response->assertSessionHas('status');
    }
}
