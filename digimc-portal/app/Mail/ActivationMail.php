<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $activationUrl;

    public $registrationDate;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->activationUrl = route('auth.activate', $user->activation_token);
        $this->registrationDate = $user->created_at->format('d.m.Y \в H:i');
    }

    public function build()
    {
        return $this->subject('Активирайте вашия акаунт в ' . config('app.name'))
            ->view('emails.activation');
    }
}
