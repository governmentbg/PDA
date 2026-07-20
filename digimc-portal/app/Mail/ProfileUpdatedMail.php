<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $changes;
    public $date;
    public string $profileUrl;

    public function __construct(User $user, array $changes, string $date) {
        $this->user = $user;
        $this->changes = $changes;
        $this->date = $date;
        $this->profileUrl = route('profile.show');
    }

    public function build()
    {
        return $this->subject('Your profile was updated on '. config('app.name'))
            ->view('emails.profile-updated');
    }
}
