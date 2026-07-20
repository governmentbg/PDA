<?php

namespace App\Mail;

use App\Models\Article;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ArticlePublishedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $articles;

    public function __construct(User $user, Collection $articles)
    {
        $this->user = $user;
        $this->articles = $articles;
    }

    public function build()
    {
        return $this->subject(__('mail.daily_news.subject', ['app' => config('app.name')]))
            ->view('emails.article-published', [
                'user' => $this->user,
                'articles' => $this->articles,
            ]);
    }
}
