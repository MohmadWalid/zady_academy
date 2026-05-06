<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:generate {month? : The month in YYYY-MM format. Defaults to current month.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate unpaid subscriptions for all active enrollments for a specific month.';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $month = $this->argument('month') ?: Carbon::now()->format('Y-m');

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error('Invalid month format. Please use YYYY-MM.');
            return 1;
        }

        $this->info("Generating subscriptions for {$month}...");

        $count = $subscriptionService->generateForMonth($month);

        $this->info("Success! Created {$count} new subscription records.");
        
        return 0;
    }
}
