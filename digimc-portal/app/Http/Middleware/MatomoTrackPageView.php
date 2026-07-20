<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MatomoTracker;

class MatomoTrackPageView
{
    public function handle(Request $request, Closure $next)
    {
        $shouldTrack = $request->isMethod('GET')
            && $request->header('DNT') !== '1'
            && !$request->expectsJson()
            && str_contains($request->headers->get('accept', ''), 'text/html');

        if (!$shouldTrack) {
            return $next($request);
        }

        try {
            $tracker = new MatomoTracker(
                (int)config('matomo.MATOMO_SITE_ID'), rtrim(config('matomo.MATOMO_URL'), '/').'/'
            );

            $tracker->disableSendImageResponse();
            $tracker->setIp($request->ip());
            $tracker->setUserAgent($request->userAgent() ?? '');
            $tracker->setUrl($request->fullUrl());

            if ($ref = $request->headers->get('referer')) {
                $tracker->setUrlReferrer($ref);
            }

            if ($request->user()) {
                $tracker->setUserId((string)$request->user()->getAuthIdentifier());
            }

            $tracker->doTrackPageView($request->route()?->getName() ?: ($request->path() ?: '/'));

            $this->forgetCountersCache();
        } catch (\Throwable $e) {
            // never break the page if tracking fails
        }

        return $next($request);
    }

    private function forgetCountersCache(): void
    {
        $siteId = (int) config('matomo.MATOMO_SITE_ID', 1);
        Cache::forget("matomo:counters:site:{$siteId}");
    }
}
