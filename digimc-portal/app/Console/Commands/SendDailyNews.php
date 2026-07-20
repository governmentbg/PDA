<?php

namespace App\Console\Commands;

use App\Services\ArticleService;
use Illuminate\Console\Command;

class SendDailyNews extends Command
{
    protected $signature = 'send:daily-news
        {--dry-run : Do not send emails; just report counts}';

    protected $description = 'Send daily email with yesterday’s published articles to subscribed users';

    public function handle()
    {

        $dryRun = $this->option('dry-run');

        $service = app(ArticleService::class);
        $message = $service->sendDailyNews($dryRun);

        if (is_array($message)) {
            $message = empty($message) ? 'No news found to send.' : implode(', ', $message);
        }
        $this->info($message);
    }
}
