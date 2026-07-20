<?php

namespace Tests\Unit;

use App\Mail\FeedbackSubmitted;
use App\Models\Setting;
use App\Rules\RecaptchaRule;
use App\Services\FeedbackService;
use App\Enums\SettingEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mockery;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecaptchaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['request']->server->set('HTTP_HOST', 'example.test');
        $this->app['request']->server->set('REMOTE_ADDR', '127.0.0.1');

        Setting::updateOrCreate(
            ['keyword' => SettingEnum::FEEDBACK_RECAPTCHA_SECRET],
            ['value' => 'test-secret']
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function validate(array $data)
    {
        return Validator::make($data, [
            'g-recaptcha-response' => ['required', 'string', new RecaptchaRule()],
        ]);
    }

    private function disableRecaptchaBypass(): void
    {
        putenv('FEEDBACK_RECAPTCHA_BYPASS_IN_TESTING=false');
    }

    private function enableRecaptchaBypass(): void
    {
        putenv('FEEDBACK_RECAPTCHA_BYPASS_IN_TESTING=true');
    }

    #[Test]
    public function bypasses_in_testing_when_bypass_enabled()
    {
        // Arrange
        $this->enableRecaptchaBypass();

        // Act
        $validator = $this->validate(['g-recaptcha-response' => 'any-token']);

        // Assert
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function fails_when_secret_is_missing_in_settings()
    {
        // Arrange
        $this->disableRecaptchaBypass();
        Config::set('feedback.recaptcha.bypass_in_testing', false);
        Setting::where('keyword', SettingEnum::FEEDBACK_RECAPTCHA_SECRET)->delete();

        // Act
        $validator = $this->validate(['g-recaptcha-response' => 'any-token']);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertSame(
            [__('feedback.captcha_failed')],
            $validator->errors()->get('g-recaptcha-response')
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    public function rule_fails_and_logs_when_verification_unsuccessful()
    {
        // Arrange
        $this->disableRecaptchaBypass();

        Setting::updateOrCreate(
            ['keyword' => SettingEnum::FEEDBACK_RECAPTCHA_SECRET],
            ['value' => 'test-secret']
        );

        $request = Request::create('http://mincul.local/feedback', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
        ]);
        $this->app->instance('request', $request);

        $host = $request->getHost();
        $ip = $request->ip();

        $recaptchaMock = Mockery::mock('overload:ReCaptcha\ReCaptcha');
        $respMock = Mockery::mock('ReCaptcha\Response');

        $recaptchaMock->shouldReceive('setExpectedHostname')->once()->with($host)->andReturnSelf();
        $recaptchaMock->shouldReceive('verify')->once()->with('bad-token', $ip)->andReturn($respMock);

        $respMock->shouldReceive('isSuccess')->once()->andReturn(false);
        $respMock->shouldReceive('getErrorCodes')->once()->andReturn(['invalid-input-response']);

        Log::spy();

        // Act
        $validator = Validator::make(
            ['g-recaptcha-response' => 'bad-token'],
            ['g-recaptcha-response' => ['required', 'string', new RecaptchaRule()]]
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertSame([__('feedback.captcha_failed')], $validator->errors()->get('g-recaptcha-response'));

        Log::shouldHaveReceived('warning')
            ->withArgs(function ($msg, $ctx) use ($host) {
                return str_contains((string)$msg, 'reCAPTCHA failed')
                    && ($ctx['host'] ?? null) === $host
                    && !empty($ctx['errors']);
            });
    }

    #[Test]
    #[RunInSeparateProcess]
    public function passes_when_verification_successful()
    {
        // Arrange
        $this->disableRecaptchaBypass();

        $req = Request::create('http://mincul.local/feedback', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
        ]);
        $this->app->instance('request', $req);

        Setting::updateOrCreate(
            ['keyword' => SettingEnum::FEEDBACK_RECAPTCHA_SECRET],
            ['value' => 'test-secret']
        );

        $host = $req->getHost();
        $ip = $req->ip();

        $recaptchaMock = Mockery::mock('overload:ReCaptcha\ReCaptcha');
        $respMock = Mockery::mock('ReCaptcha\Response');

        $recaptchaMock->shouldReceive('setExpectedHostname')->once()->with($host)->andReturnSelf();
        $recaptchaMock->shouldReceive('verify')->once()->with('good-token', $ip)->andReturn($respMock);

        $respMock->shouldReceive('isSuccess')->once()->andReturn(true);

        // Act
        $validator = Validator::make(
            ['g-recaptcha-response' => 'good-token'],
            ['g-recaptcha-response' => ['required', 'string', new RecaptchaRule()]]
        );

        // Assert
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function service_returns_early_when_mail_disabled()
    {
        // Arrange
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_MAIL_ENABLED], ['value' => '0']);
        Mail::fake();

        $service = app(FeedbackService::class);

        // Act
        $service->sendFeedback([
            'subject' => 'Test subject',
            'category' => 'Problem',
            'description' => 'Some description',
            'contact_email' => 'test@example.com',
            'name' => 'Tester',
        ]);

        // Assert
        Mail::assertNothingSent();
    }

    #[Test]
    public function service_reads_addresses_from_settings_and_sends()
    {
        // Arrange
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_MAIL_ENABLED], ['value' => '1']);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_TO_CONTACT_EMAIL], ['value' => 'to@example.test']);
        Setting::updateOrCreate(['keyword' => SettingEnum::FEEDBACK_FROM_CONTACT_EMAIL], ['value' => 'from@example.test']);

        Mail::fake();

        $service = app(FeedbackService::class);

        // Act
        $service->sendFeedback([
            'subject' => 'Hello',
            'category' => 'Problem',
            'description' => 'Long enough',
            'contact_email' => 'test@email.com',
            'name' => 'Test',
        ]);

        // Assert
        Mail::assertSent(FeedbackSubmitted::class, 1);
    }

    #[Test]
    public function queries_by_keyword_column_not_id()
    {
        DB::table('setting')->insert([
            ['keyword' => 'unrelated', 'value' => 'foo'],
            ['keyword' => SettingEnum::FEEDBACK_FROM_CONTACT_EMAIL, 'value' => 'sender@example.com'],
        ]);

        $this->assertSame(
            'sender@example.com',
            SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_FROM_CONTACT_EMAIL)
        );
    }
}
