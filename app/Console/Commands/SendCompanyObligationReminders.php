<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Notifications\CompanyObligationReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SendCompanyObligationReminders extends Command
{
    protected $signature = 'compliance:send-reminders {--business= : Restrict to a business id}';

    protected $description = 'Send countdown and overdue escalation reminders for core CRO obligations';

    /**
     * @var array<int, string>
     */
    protected array $coreCodes = ['B1', 'B10', 'B2', 'AGM'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->startOfDay();
        $businessId = $this->option('business');

        $businesses = Business::query()
            ->when($businessId, fn ($q) => $q->where('id', (int) $businessId))
            ->get();

        if ($businesses->isEmpty()) {
            $this->warn('No business found for reminder processing.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($businesses as $business) {
            $users = $business->users()->get();
            if ($users->isEmpty()) {
                continue;
            }

            $obligations = DB::table('company_cro_document as p')
                ->join('companies as c', 'c.id', '=', 'p.company_id')
                ->join('cro_doc_definitions as d', 'd.id', '=', 'p.cro_doc_definition_id')
                ->where('c.business_id', $business->id)
                ->where(function ($q) {
                    $q->where('c.active', true)->orWhereNull('c.active');
                })
                ->whereIn('d.code', $this->coreCodes)
                ->whereNotNull('p.due_date')
                ->select([
                    'c.id as company_id',
                    'c.name as company_name',
                    'c.company_number',
                    'd.id as definition_id',
                    'd.code as doc_code',
                    'd.name as doc_name',
                    'p.status',
                    'p.risk_level',
                    'p.due_date',
                ])
                ->get();

            foreach ($obligations as $row) {
                $dueDate = Carbon::parse((string) $row->due_date)->startOfDay();
                $daysUntilDue = $today->diffInDays($dueDate, false);

                $countdownDays = [60, 30, 7];
                foreach ($countdownDays as $days) {
                    $triggerDate = $dueDate->copy()->subDays($days);

                    if ($today->lt($triggerDate) || $daysUntilDue < 0) {
                        continue;
                    }

                    foreach ($users as $user) {
                        if (!$this->isPreferenceEnabled($user, 'document_deadlines')) {
                            continue;
                        }

                        $type = "countdown_{$days}";
                        if ($this->wasReminderSent((int) $user->id, (int) $row->company_id, (int) $row->definition_id, $type, $triggerDate)) {
                            continue;
                        }

                        $payload = [
                            'type' => $type,
                            'company_id' => (int) $row->company_id,
                            'company_name' => (string) $row->company_name,
                            'company_number' => (string) $row->company_number,
                            'doc_code' => (string) $row->doc_code,
                            'doc_name' => (string) $row->doc_name,
                            'due_date' => $dueDate->toDateString(),
                            'days_remaining' => $daysUntilDue,
                            'title' => "{$row->doc_code} deadline in {$daysUntilDue} days",
                            'message' => "{$row->doc_code} for {$row->company_name} is due on {$dueDate->format('d M Y')}.",
                            'risk_level' => (string) $row->risk_level,
                            'status' => (string) $row->status,
                        ];

                        $user->notify(new CompanyObligationReminderNotification($payload));
                        $this->storeSentReminder((int) $user->id, (int) $row->company_id, (int) $row->definition_id, $type, $triggerDate, $dueDate);
                        $sent++;
                    }
                }

                if ($daysUntilDue >= 0) {
                    continue;
                }

                $overdueDays = abs($daysUntilDue);
                $escalationThresholds = [
                    1 => 'overdue_1',
                    7 => 'overdue_7',
                    30 => 'overdue_30',
                ];

                foreach ($escalationThresholds as $threshold => $type) {
                    if ($overdueDays < $threshold) {
                        continue;
                    }

                    $triggerDate = $dueDate->copy()->addDays($threshold);

                    foreach ($users as $user) {
                        if (!$this->isPreferenceEnabled($user, 'overdue_escalation')) {
                            continue;
                        }

                        if ($this->wasReminderSent((int) $user->id, (int) $row->company_id, (int) $row->definition_id, $type, $triggerDate)) {
                            continue;
                        }

                        $payload = [
                            'type' => $type,
                            'company_id' => (int) $row->company_id,
                            'company_name' => (string) $row->company_name,
                            'company_number' => (string) $row->company_number,
                            'doc_code' => (string) $row->doc_code,
                            'doc_name' => (string) $row->doc_name,
                            'due_date' => $dueDate->toDateString(),
                            'days_overdue' => $overdueDays,
                            'title' => "{$row->doc_code} overdue ({$overdueDays} days)",
                            'message' => "{$row->doc_code} for {$row->company_name} is overdue by {$overdueDays} days.",
                            'risk_level' => (string) $row->risk_level,
                            'status' => (string) $row->status,
                        ];

                        $user->notify(new CompanyObligationReminderNotification($payload));
                        $this->storeSentReminder((int) $user->id, (int) $row->company_id, (int) $row->definition_id, $type, $triggerDate, $dueDate);
                        $sent++;
                    }
                }
            }
        }

        $this->info("Compliance reminders processed. Notifications sent: {$sent}");
        return self::SUCCESS;
    }

    protected function isPreferenceEnabled(User $user, string $key): bool
    {
        $setting = UserNotificationSetting::firstOrCreate(
            ['user_id' => $user->id, 'notification_key' => $key],
            ['is_enabled' => true]
        );

        return (bool) $setting->is_enabled;
    }

    protected function wasReminderSent(
        int $userId,
        int $companyId,
        int $definitionId,
        string $type,
        Carbon $triggerDate
    ): bool {
        return DB::table('company_obligation_reminders')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('cro_doc_definition_id', $definitionId)
            ->where('reminder_type', $type)
            ->whereDate('trigger_date', $triggerDate->toDateString())
            ->exists();
    }

    protected function storeSentReminder(
        int $userId,
        int $companyId,
        int $definitionId,
        string $type,
        Carbon $triggerDate,
        Carbon $dueDate
    ): void {
        DB::table('company_obligation_reminders')->upsert(
            [[
                'user_id' => $userId,
                'company_id' => $companyId,
                'cro_doc_definition_id' => $definitionId,
                'reminder_type' => $type,
                'trigger_date' => $triggerDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]],
            ['user_id', 'company_id', 'cro_doc_definition_id', 'reminder_type', 'trigger_date'],
            ['due_date', 'sent_at', 'updated_at']
        );
    }
}
