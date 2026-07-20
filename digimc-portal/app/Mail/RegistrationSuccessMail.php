<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->loginUrl = route('auth.login');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Успешна регистрация в ' . config('app.name'))
            ->view('emails.registration-success');
    }
}
