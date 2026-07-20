<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use Illuminate\Console\Command;

class SyncPaymentStatuses extends Command
{
    protected $signature = 'payments:sync-statuses';

    protected $description = 'Sync pending payment statuses with eGov provider';

    public function handle()
    {
        $this->info('Starting payment status synchronization...');

        try {
            $service = app(PaymentService::class);
            $service->syncStatuses();

            $this->info('Synchronization completed successfully.');
        } catch (\Exception $e) {
            \Log::error('Synchronization failed: ' . $e->getMessage());
            $this->error('Synchronization failed: ' . $e->getMessage());
        }
    }
}
