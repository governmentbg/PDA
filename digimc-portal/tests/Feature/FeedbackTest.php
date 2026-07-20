<?php

namespace Tests\Feature;

use App\Mail\FeedbackSubmitted;
use App\Providers\FeedbackServiceProvider;
use App\Rules\RecaptchaRule;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Setting;
use App\Enums\FeedbackCategoryEnum;
use App\Enums\SettingEnum;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');

        Config::set('mail.default', 'array');
        Config::set('mail.from.address', 'no-reply@portal.test');
        Config::set('mail.from.name', 'Portal');

        Setting::updateOrCreate(['keyword' => 'to_contact_email'], ['value' => 'test@email.com']);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_SUBJECT_MAX], ['value' => 120]);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_DESCRIPTION_MAX], ['value' => 1000]);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_EMAIL_MAX], ['value' => 120]);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_NAME_MAX], ['value' => 80]);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_RECAPTCHA_SITE_KEY], ['value' => 'test-site-key']);

        $this->app->bind(RecaptchaRule::class, fn() => new class {
            public function passes()
            {
                return true;
            }

            public function message()
            {
                return 'ok';
            }
        });
    }

    private function validPayload(array $overrides = []): array
    {
        $defaultCategory = FeedbackCategoryEnum::PROBLEM;

        return array_merge([
            'subject' => 'Test subject',
            'category' => $defaultCategory,
            'description' => 'A helpful description',
            'contact_email' => 'test@example.com',
            'name' => 'Test User',
            'g-recaptcha-response' => 'dummy-token',
        ], $overrides);
    }

    #[Test]
    public function returns_per_field_errors_on_validation_failure()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);

        // Act
        $response = $this->postJson(route('feedback.store'), []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'subject',
                    'category',
                    'description',
                    'contact_email',
                    'name',
                    'g-recaptcha-response',
                ],
            ]);
    }

    #[Test]
    public function sends_email_and_returns_success_message_in_en()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();

        // Act
        $response = $this->postJson(route('feedback.store'), $this->validPayload());

        // Assert
        $response->assertOk()->assertJson([
            'message' => __('feedback.modal.success'),
        ]);

        Mail::assertSent(FeedbackSubmitted::class, 1);
    }

    #[Test]
    public function success_message_is_localized_in_bg()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);
        app()->setLocale('bg');
        Mail::fake();

        // Act
        $response = $this->postJson(route('feedback.store'), $this->validPayload());

        // Assert
        $response->assertOk()->assertJson([
            'message' => __('feedback.modal.success'),
        ]);
    }

    #[Test]
    public function localized_validation_messages_are_returned_in_bg()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);
        app()->setLocale('bg');

        // Act & Assert
        $response = $this->postJson(route('feedback.store'), []);
        $response->assertStatus(422);

        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors['subject'][0]);
        $this->assertSame(__('feedback.captcha_failed'), $errors['g-recaptcha-response'][0] ?? null);
    }

    #[Test]
    public function returns_500_when_destination_email_is_missing()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();

        Setting::updateOrCreate(['keyword' => 'to_contact_email'], ['value' => '']);

        // Act
        $response = $this->postJson(route('feedback.store'), $this->validPayload());

        // Assert
        $response->assertStatus(500);
        Mail::assertNothingSent();
    }

    #[Test]
    public function rate_limiting_returns_429_after_threshold()
    {
        // Arrange
        RateLimiter::for('feedback', function ($request) {
            return [Limit::perMinute(2)->by($request->ip())];
        });
        Mail::fake();

        // Act & Assert
        $this->postJson(route('feedback.store'), $this->validPayload())->assertOk();
        $this->postJson(route('feedback.store'), $this->validPayload())->assertOk();
        $this->postJson(route('feedback.store'), $this->validPayload())->assertStatus(429);

        Mail::assertSent(FeedbackSubmitted::class, 2);
    }

    #[Test]
    public function email_html_contains_key_fields()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();

        $payload = $this->validPayload([
            'subject' => 'Test issue',
            'contact_email' => 'test@example.com',
            'category' => FeedbackCategoryEnum::PROBLEM,
        ]);

        // Act
        $this->postJson(route('feedback.store'), $payload)->assertOk();

        // Assert
        Mail::assertSent(FeedbackSubmitted::class, function (FeedbackSubmitted $message) use ($payload) {
            $html = $message->render();
            return str_contains($html, 'mailto:' . $payload['contact_email'])
                && str_contains($html, e($payload['subject']))
                && str_contains($html, __('feedback.categories.' . $payload['category']));
        });
    }

    #[Test]
    public function it_returns_500_when_mail_send_throws()
    {
        // Arrange
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();

//        $logSpy = Log::spy();

        Mail::shouldReceive('to')
            ->once()
            ->with('test@email.com')
            ->andReturnSelf();
        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new \RuntimeException('SMTP boom'));

        // Act
        $response = $this->postJson(route('feedback.store'), $this->validPayload());

        // Assert
        $response->assertStatus(500)->assertJson([
            'message' => __('feedback.modal.generic_error'),
        ]);

//        $logSpy->shouldHaveReceived('error')
//            ->withArgs(fn($message, array $context) => str_contains((string)$message, 'Feedback email failed')
//                && ($context['error'] ?? null) === 'SMTP boom'
//            );
    }

    #[Test]
    public function feedback_rate_limiter_is_registered_with_values_from_service_provider()
    {
        // Arrange
        (new FeedbackServiceProvider($this->app))->boot();

        // Act & Assert
        $req = Request::create('http://mincul.local/anything', 'GET', [], [], [], ['REMOTE_ADDR' => '203.0.113.42']);

        $callback = RateLimiter::limiter('feedback');
        $this->assertIsCallable($callback);

        $limits = $callback($req);

        $this->assertIsArray($limits);
        $this->assertCount(2, $limits);
        $this->assertInstanceOf(Limit::class, $limits[0]);
        $this->assertInstanceOf(Limit::class, $limits[1]);
    }
}
