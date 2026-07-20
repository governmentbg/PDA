<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewArticlePublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $article;
    public $user;

    public function __construct($article, $user)
    {
        $this->article = $article;
        $this->user = $user;
    }

    public function build()
    {
        $appName = config('app.name');

        return $this->subject("Нова публикация в {$appName}")
            ->view('emails.new-article-published')
            ->with([
                'user' => $this->user,
                'article' => $this->article,
                'appName' => $appName,
            ]);
    }
}
