<?php

namespace App\Rules;

use App\Enums\SettingEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use ReCaptcha\ReCaptcha;

class RecaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Allow bypass in tests
        $bypass = (bool) config('feedback.recaptcha.bypass_in_testing');
        if (app()->environment('testing') && $bypass) {
            return;
        }

        $secret = SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_RECAPTCHA_SECRET);
        if (empty($secret)) {
            $fail(__('feedback.captcha_failed'));
            return;
        }

        $recaptcha = new ReCaptcha($secret);

        // match host when Google returns it
        $expectedHost = request()->getHost();
        $recaptcha->setExpectedHostname($expectedHost);

        $resp = $recaptcha->verify($value, request()->ip());

        if (!$resp->isSuccess()) {
            Log::warning('reCAPTCHA failed', [
                'errors' => $resp->getErrorCodes(),
                'host'   => $expectedHost,
            ]);
            $fail(__('feedback.captcha_failed'));
            return;
        }

    }

    public function message(): string
    {
        return __('feedback.captcha_failed');
    }
}
