<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Apply the current locale from the session (fallback 'bg').
     *
     * @param  Request  $request
     * @param  Closure(Request):mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $request->session()->get('locale') ?? 'bg';

        app()->setLocale($locale);

        if (!$request->session()->has('locale')) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
