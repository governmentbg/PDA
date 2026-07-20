<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $articles;

    public function __construct($user, $articles)
    {
        $this->user = $user;
        $this->articles = $articles;
    }

    public function build()
    {
        $appName = config('app.name');

        return $this->subject("Новини от седмицата в {$appName}")
            ->view('emails.weekly-summary')
            ->with([
                'user' => $this->user,
                'articles' => $this->articles,
                'appName' => $appName,
            ]);
    }
}
