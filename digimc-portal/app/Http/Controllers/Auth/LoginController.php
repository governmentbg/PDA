<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\User;
use ReCaptcha\ReCaptcha;
use App\Enums\SettingEnum;

class LoginController extends Controller
{
    public function showLoginForm()
    {

        $recaptchaSiteKey = SettingEnum::getValueByKeyword(SettingEnum::RECAPTCHA_SITE_KEY)
            ?? config('services.recaptcha.site_key');
        $recaptchaEnabled = SettingEnum::getValueByKeyword(SettingEnum::RECAPTCHA_ENABLED)
            ?? config('services.recaptcha.enabled');

        return view('auth.login', compact('recaptchaSiteKey', 'recaptchaEnabled'));
    }

    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $recaptchaSecret = SettingEnum::getValueByKeyword(SettingEnum::RECAPTCHA_SECRET)
            ?? config('services.recaptcha.secret_key');

        $recaptchaEnabled = SettingEnum::getValueByKeyword(SettingEnum::RECAPTCHA_ENABLED) && !empty($recaptchaSecret);



        if ($recaptchaEnabled && Session::get('login_attempts', 0) >= 3) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules);


        /** @var \App\Models\User|null $user */
        $user = User::where('email', $request->email)->first();



        if (!is_null($user)) {
            /** @var \Carbon\Carbon|null $lockedUntil */
            $lockedUntil = $user->locked_until;
            if($lockedUntil && $lockedUntil->gt(Carbon::now()))
            {
                return back()->withErrors(['email' => __('general.auth.account_locked')]);
            }
        }

        if ($recaptchaEnabled && Session::get('login_attempts', 0) >= 3) {

            $recaptcha = new ReCaptcha($recaptchaSecret);

            $resp = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());


            if (!$resp->isSuccess()) {
                return back()->withErrors(['captcha' => __('general.auth.captcha_failed')]);
            }
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            Session::forget('login_attempts');
            return redirect()->intended(route('profile.galleries.index'));
        }


        $attempts = Session::get('login_attempts', 0) + 1;
        Session::put('login_attempts', $attempts);

        if ($attempts >= 5 && !is_null($user)) {
            $user->locked_until = Carbon::now()->addMinutes(30);
            $user->save();
            return back()->withErrors(['email' => __('general.auth.account_locked')]);
        }

        return back()->withErrors(['email' => __('general.auth.invalid_credentials')]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', __('general.auth.logged_out'));
    }
}
