<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * Auto-generate unpaid subscriptions for every active enrollment in the given month.
     *
     * Uses firstOrCreate keyed on (student_id, group_id, month) so it is safe
     * to run multiple times without creating duplicates (Implementation-Rules §7).
     *
     * Only processes enrollments where active = true AND deleted_at IS NULL.
     *
     * @param  string $month  YYYY-MM format (e.g. "2025-05")
     * @return int  Number of newly created subscription rows
     */
    public function generateForMonth(string $month): int
    {
        $created = 0;

        Enrollment::query()
            ->where('active', true)
            ->whereNull('deleted_at')
            ->each(function (Enrollment $enrollment) use ($month, &$created) {
                $sub = Subscription::firstOrCreate(
                    [
                        'student_id' => $enrollment->student_id,
                        'group_id'   => $enrollment->group_id,
                        'month'      => $month,
                    ],
                    ['status' => 'unpaid']
                );

                if ($sub->wasRecentlyCreated) {
                    $created++;
                }
            });

        return $created;
    }
}
