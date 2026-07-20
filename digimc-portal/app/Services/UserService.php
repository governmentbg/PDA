<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationMail;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function createUser(array $userData): User
    {
        $userData['password'] = Hash::make($userData['password']);
        $userData['activation_token'] = Str::random(32);
        $userData['activation_token_expires_at'] = now()->addMinutes(30);
        $userData['name'] = $userData['first_name'] . ' ' . $userData['last_name'];

        $user = User::create($userData);

        try {
            $this->sendActivationEmail($user);
        } catch (Exception $e) {
            Log::error('Грешка при изпращане на активационен имейл: ' . $e->getMessage());
        }

        return $user;
    }

    protected function sendActivationEmail(User $user): void
    {
        Mail::to($user->email)->send(new ActivationMail($user));
    }

    public function resendActivationEmail(string $email): bool
    {
        $user = User::where('email', $email)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return false;
        }

        $user->update([
            'activation_token' => Str::random(32),
            'activation_token_expires_at' => now()->addMinutes(30),
        ]);

        try {
            $this->sendActivationEmail($user);
            return true;
        } catch (\Exception $e) {
            \Log::error('Грешка при повторно изпращане на активационен имейл: ' . $e->getMessage());
            return false;
        }
    }


}
