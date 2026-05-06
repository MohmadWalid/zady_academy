<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class GenerateMonthlySubscriptions extends Command
{
    /**
     * Scheduled: 1st of each month at 00:05 (Implementation-Rules §7, System-Design §8).
     * Can also be triggered manually with an optional --month override for backfills.
     */
    protected $signature = 'subscriptions:generate
                            {--month= : الشهر بصيغة YYYY-MM (الافتراضي: الشهر الحالي)}';

    protected $description = 'توليد الاشتراكات الشهرية لجميع التسجيلات النشطة';

    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $month = $this->option('month') ?? now()->format('Y-m');

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error('صيغة الشهر غير صحيحة. المثال الصحيح: 2025-05');
            return Command::FAILURE;
        }

        $this->info("جاري توليد الاشتراكات لشهر {$month} ...");

        $count = $this->subscriptionService->generateForMonth($month);

        $this->info("✓ تم إنشاء {$count} اشتراك جديد لشهر {$month}.");

        return Command::SUCCESS;
    }
}
