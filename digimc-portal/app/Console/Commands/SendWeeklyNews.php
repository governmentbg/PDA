<?php

namespace App\Console\Commands;

use App\Services\ArticleService;
use Illuminate\Console\Command;

class SendWeeklyNews extends Command
{
    protected $signature = 'emails:weekly-news';
    protected $description = 'Изпраща седмичните новини на всички абонирани потребители.';

    public function handle(ArticleService $articleService)
    {
        $this->info('Стартира изпращането на седмичните новини...');
        $articleService->sendWeeklyNews();
        $this->info('Готово!');
    }
}
