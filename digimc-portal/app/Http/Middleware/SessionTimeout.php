<?php

namespace App\Http\Middleware;

use App\Enums\SettingEnum;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }


        $timeout = (int) SettingEnum::getValueByKeyword(SettingEnum::SESSION_LIFETIME);
        $lastActivity = session('last_activity');


        if ($lastActivity) {
            if (!$lastActivity instanceof Carbon) {
                $lastActivity = Carbon::parse($lastActivity);
            }

            $timeDifferenceInMinutes = $lastActivity->diffInMinutes(now());

            if ($timeDifferenceInMinutes >= $timeout) {

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_EXPIRED_MESSAGE);
                return redirect()->route('session-expired')->with('status', $message);
            }
        }


        session(['last_activity' => now()]);

        return $next($request);
    }
}
