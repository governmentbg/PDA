<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyNewsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $news;

    public function __construct(User $user, Collection $news)
    {
        $this->user = $user;
        $this->news = $news;
    }

    public function build()
    {
        return $this->subject(__('mail.weekly_news.subject', ['app' => config('app.name')]))
            ->view('emails.news.weekly')
            ->with([
                'user' => $this->user,
                'news' => $this->news,
            ]);
    }
}
