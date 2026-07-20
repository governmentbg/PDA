<?php

namespace App\Providers;

use App\Enums\SettingEnum;
use App\View\Components\FeedbackForm;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FeedbackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::component('feedback-form', FeedbackForm::class);

        $perMinute = config('feedback.route.throttle.per_minute');
        $perHour = config('feedback.route.throttle.per_hour');

        RateLimiter::for('feedback', function (Request $request) use ($perMinute, $perHour) {
            $customResponse = function (Request $request, array $headers) {
                $locale = app()->getLocale() ?: config('app.locale');
                return response()->json([
                    'message' => __('feedback.too_many_attempts', [], $locale),
                ], 429, $headers);
            };

            return [
                Limit::perMinute($perMinute)->by($request->ip())->response($customResponse),
                Limit::perHour($perHour)->by($request->ip())->response($customResponse),
            ];
        });

    }
}
