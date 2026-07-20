<?php

namespace Tests\Unit;

use App\Mail\PasswordChangedMail;
use App\Mail\ProfileUpdatedMail;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_names_and_toggles_and_sends_mail_when_changed()
    {
        // Arrange
        Mail::fake();

        $user = User::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'wants_notifications' => false,
            'subscribed_news' => false,
            'subscribed_weekly' => false,
        ]);
        $this->actingAs($user);

        $request = Request::create('/profile', 'POST', [
            'first_name' => '  Jane  ',
            'last_name' => '  Smith ',
            'wants_notifications' => '1',
            'subscribed_news' => '1',
            'subscribed_weekly' => '1',
        ]);

        // Act
        $service = new ProfileService();
        $service->updateProfile($request);

        // Assert
        $user->refresh();
        $this->assertSame('Jane', $user->first_name);
        $this->assertSame('Smith', $user->last_name);
        $this->assertTrue($user->wants_notifications);
        $this->assertTrue($user->subscribed_news);
        $this->assertTrue($user->subscribed_weekly);

        Mail::assertSent(ProfileUpdatedMail::class, function ($mailable) use ($user) {
            return method_exists($mailable, 'hasTo')
                ? $mailable->hasTo($user->email)
                : true;
        });
    }

    #[Test]
    public function it_does_not_send_mail_when_nothing_changed()
    {
        // Arrange
        Mail::fake();

        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $this->actingAs($user);

        // Post identical values
        $request = Request::create('/profile', 'POST', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Act
        $service = new ProfileService();
        $service->updateProfile($request);

        // Assert
        $user->refresh();
        $this->assertSame('John', $user->first_name);
        $this->assertSame('Doe', $user->last_name);

        Mail::assertNotSent(ProfileUpdatedMail::class);
    }

    #[Test]
    public function it_throws_if_not_authenticated()
    {
        $request = Request::create('/profile', 'POST', [
            'first_name' => 'X',
            'last_name' => 'Y',
            'wants_notifications' => '1',
            'subscribed_news' => '0',
            'subscribed_weekly' => '1',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('no user logged in');
        $this->expectExceptionCode(1);

        $service = new ProfileService();
        $service->updateProfile($request);
    }

    #[Test]
    public function it_hashes_password_with_bcrypt_and_saves_user()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('ok'),
        ]);
        $this->actingAs($user);

        // Act
        $service = new ProfileService();
        $service->updatePassword('NewPass123');

        // Assert
        $this->assertTrue(password_verify('NewPass123', $user->password));
    }

    #[Test]
    public function it_validates_that_there_is_a_logged_in_user_before_changing_password()
    {

        try {
            // Act
            $service = new ProfileService();
            $service->updatePassword("sdalkjdslkajld");

            // Assert
        } catch (\Exception $exception) {
            //then assert exception
            $this->assertSame($exception->getCode(), 1);

            return true;
        }

        $this->fail('no Exception passed in this test');

    }

    #[Test]
    public function it_sends_notification_for_changed_password()
    {
        // Arrange
        Mail::fake();
        $user = User::factory()->create([
            'password' => Hash::make('ok'),
            'wants_notifications' => '1',
        ]);

        $this->actingAs($user);

        // Act
        $service = new ProfileService();
        $service->updatePassword("NewPass123");

        // Assert hash
        $this->assertTrue(password_verify('NewPass123', $user->fresh()->password));
        $this->assertStringStartsWith('$2y$', $user->fresh()->password);

        // Assert
        Mail::assertSent(PasswordChangedMail::class, function ($mailable) use ($user) {
            return method_exists($mailable, 'hasTo') ? $mailable->hasTo($user->email) : true;
        });
    }


}
