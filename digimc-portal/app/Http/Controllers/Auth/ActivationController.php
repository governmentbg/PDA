<?php

namespace App\Http\Controllers\Auth;

use App\Mail\RegistrationSuccessMail;
use App\Models\User;
use App\Mail\ActivationMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    public function activate($token)
    {
        $user = User::where('activation_token', $token)
            ->where('activation_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            $expiredUser = User::where('activation_token', $token)->first();

            if ($expiredUser) {
                $expiredUser->update([
                    'activation_token' => Str::random(32),
                    'activation_token_expires_at' => now()->addMinutes(30),
                ]);

                Mail::to($expiredUser->email)->send(new ActivationMail($expiredUser));

                return redirect()->route('auth.register') //todo
                    ->with('error', __('general.auth.activation_link_expired'));
            }

            return redirect()->route('auth.register') //todo
                ->with('error', __('general.auth.invalid_activation_link'));
        }

        $user->update([
            'activation_token' => null,
            'activation_token_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        Mail::to($user->email)->send(new RegistrationSuccessMail($user));

        \Alert::success(__('general.auth.success'), __('general.auth.account_activated'));
        return redirect()->route('auth.login');
    }

    public function showResendForm()
    {
        return view('auth.resend-activation');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => __('general.auth.user_not_found')
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $request->email)->first();

//        if (!is_null($user) && $user->is_active) {
//            return back()->with('error', 'Този акаунт вече е активиран. Можете да влезете в системата.');
//        }


        $user->update([
            'activation_token' => Str::random(32),
            'activation_token_expires_at' => now()->addMinutes(30),
        ]);

        Mail::to($user->email)->send(new ActivationMail($user));

        return back()->with('success', __('general.auth.activation_email_sent'));
    }
}
