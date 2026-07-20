<?php

namespace Feature;

use App\Mail\ProfileUpdatedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_sends_an_email_when_profile_is_updated()
    {
        // Arrange
        Mail::fake();

        $user = User::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'password' => Hash::make('ok'),
            'wants_notifications' => false,
            'subscribed_news' => true,
            'subscribed_weekly' => false,
        ]);

        $this->actingAs($user);

        $response = $this->from(route('profile.edit'))->post(route('profile.update'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'wants_notifications' => '1',
            'subscribed_news' => '1',
            'subscribed_weekly' => '1',
            'current_password' => 'ok',
        ]);

        $response->assertRedirect(route('profile.show'))
            ->assertSessionHas('status');

        // Assert
        Mail::assertSent(ProfileUpdatedMail::class, function ($mailable) use ($user) {
            return method_exists($mailable, 'hasTo')
                ? $mailable->hasTo($user->email)
                : true;
        });
    }

}
