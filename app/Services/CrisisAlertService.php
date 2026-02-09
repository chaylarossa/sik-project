<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\User;
use App\Notifications\CrisisHighPriorityNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class CrisisAlertService
{
    /**
     * Send alert for high priority crisis report with throttling/idempotency.
     */
    public function sendHighPriorityAlert(CrisisReport $report): void
    {
        // 1. Check if urgency is high
        if (!$report->urgencyLevel?->is_high_priority) {
            return;
        }

        // 2. Throttling/Idempotency: Prevent duplicate alerts for the same report
        // Key expires in 24 hours (sufficient for "same report" context)
        $cacheKey = "high_priority_alert_sent_{$report->id}";
        
        if (Cache::has($cacheKey)) {
            return;
        }

        // 3. Identify Recipients: Administrator & Pimpinan
        $recipients = User::role([RoleName::Administrator->value, RoleName::Pimpinan->value])
            ->where('id', '!=', $report->created_by) // Optional: don't notify self if reporter is admin (unlikely but good practice)
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        // 4. Send Notification
        Notification::send($recipients, new CrisisHighPriorityNotification($report));

        // 5. Mark as sent
        Cache::put($cacheKey, true, now()->addHours(24));
    }
}
