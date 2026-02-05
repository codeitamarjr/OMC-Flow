<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CroDocDefinition;
use App\Services\Core\CroSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshSingleCompanyFromCro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [30, 120, 300];

    public function __construct(public int $companyId)
    {
        $this->onQueue(config('services.cro.queue', 'default'));
    }

    /**
     * Execute the job.
     */
    public function handle(CroSearchService $cro): void
    {
        $company = Company::find($this->companyId);

        if ($company === null || !$this->isValidCompanyNumber($company->company_number)) {
            return;
        }

        try {
            $details = $this->fetchCompanyDetails($cro, $company->company_number);

            if ($details === null) {
                return;
            }

            $company->update($this->mapDetailsToAttributes($details));

            $submissions = $this->fetchCompanySubmissions($cro, $company->company_number);
            $this->syncFilingHistory($company, $submissions);

            $officers = $this->extractOfficerSnapshot($details);
            $this->syncOfficerSnapshot($company, $officers);

            $this->syncObligations($company, $submissions, $officers);
        } catch (Throwable $e) {
            Log::warning('Failed refreshing company from CRO', [
                'company_id' => $this->companyId,
                'company_number' => $company->company_number,
                'attempt' => $this->attempts(),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function fetchCompanyDetails(CroSearchService $cro, string $companyNumber): ?array
    {
        $details = $cro->getCompanyDetails($companyNumber);

        return empty($details) ? null : $details;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fetchCompanySubmissions(CroSearchService $cro, string $companyNumber): array
    {
        $submissions = $cro->getCompanySubmissions($companyNumber);

        if (is_array($submissions) && $this->isListArray($submissions) && !empty($submissions)) {
            return $submissions;
        }

        // Fallback for empty or non-list payloads from upstream.
        $fallback = $cro->searchCompanySubmissions($companyNumber, 'C');

        return is_array($fallback) && $this->isListArray($fallback) ? $fallback : [];
    }

    /**
     * @param array<int, array<string, mixed>> $submissions
     */
    protected function syncFilingHistory(Company $company, array $submissions): void
    {
        foreach ($submissions as $doc) {
            if (!isset($doc['sub_num'], $doc['doc_num'])) {
                continue;
            }

            $company->submissionDocuments()->updateOrCreate([
                'sub_num' => (string) $doc['sub_num'],
                'doc_num' => (string) $doc['doc_num'],
            ], [
                'sub_type_desc' => $doc['sub_type_desc'] ?? null,
                'doc_type_desc' => $doc['doc_type_desc'] ?? null,
                'sub_status_desc' => $doc['sub_status_desc'] ?? null,
                'sub_received_date' => $this->formatCroDateTime($doc['sub_received_date'] ?? null),
                'sub_effective_date' => $this->formatCroDateTime($doc['sub_effective_date'] ?? null),
                'acc_year_to_date' => $this->formatCroDateTime($doc['acc_year_to_date'] ?? null),
                'scan_date' => $this->formatCroDateTime($doc['scan_date'] ?? null),
                'num_pages' => $doc['num_pages'] ?? null,
                'doc_id' => $doc['doc_id'] ?? null,
                'file_size' => $doc['file_size_bytes'] ?? null,
                'scanned' => isset($doc['scanned']) ? strtoupper((string) $doc['scanned']) === 'Y' : false,
            ]);
        }
    }

    /**
     * @param array<string, mixed> $details
     * @return array<int, array<string, mixed>>
     */
    protected function extractOfficerSnapshot(array $details): array
    {
        $candidates = [
            $details['officers'] ?? null,
            $details['company_officers'] ?? null,
            $details['officer_details'] ?? null,
            $details['officer_list'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (!is_array($candidate) || !$this->isListArray($candidate)) {
                continue;
            }

            $normalized = [];

            foreach ($candidate as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $normalized[] = [
                    'name' => $this->uppercase($entry['name'] ?? $entry['officer_name'] ?? null),
                    'role' => $this->uppercase($entry['role'] ?? $entry['officer_type'] ?? null),
                    'appointed_at' => $this->formatCroDate($entry['appointed_date'] ?? $entry['appointment_date'] ?? null),
                    'ceased_at' => $this->formatCroDate($entry['ceased_date'] ?? $entry['resigned_date'] ?? null),
                ];
            }

            if (!empty($normalized)) {
                return $normalized;
            }
        }

        return [];
    }

    /**
     * @param array<int, array<string, mixed>> $officers
     */
    protected function syncOfficerSnapshot(Company $company, array $officers): void
    {
        $payload = [
            'cro_officers_synced_at' => now(),
        ];

        if (!empty($officers)) {
            $payload['cro_officers_snapshot'] = $officers;
        }

        $company->update($payload);
    }

    /**
     * @param array<int, array<string, mixed>> $submissions
     * @param array<int, array<string, mixed>> $officers
     */
    protected function syncObligations(Company $company, array $submissions, array $officers): void
    {
        $definitions = $this->ensureDefaultDefinitions($company->business_id);

        if ($definitions->isEmpty()) {
            return;
        }

        $latestByCode = $this->latestSubmissionByCode($submissions);
        $officerChangeEventDate = $this->latestOfficerChangeEventDate($officers);

        $company->croDocDefinitions()->syncWithoutDetaching(
            $definitions->mapWithKeys(fn (CroDocDefinition $def) => [
                $def->id => [
                    'completed' => false,
                    'status' => 'missing',
                    'risk_level' => 'medium',
                ],
            ])->toArray()
        );

        $today = now()->startOfDay();
        $ardDate = $company->next_annual_return ? Carbon::parse($company->next_annual_return)->startOfDay() : null;

        foreach ($definitions as $definition) {
            $code = strtoupper($definition->code);
            $latest = $latestByCode[$code] ?? null;
            $lastFiledAt = $latest !== null ? $this->extractSubmissionDate($latest) : null;
            $dueDate = $this->calculateDueDate($code, $definition->days_from_ard, $ardDate, $officerChangeEventDate);

            $status = 'missing';
            $risk = 'medium';
            $completed = false;
            $completedAt = null;
            $notes = null;

            if ($code === 'B1') {
                [$status, $risk, $completed, $notes] = $this->assessAnnualReturn(
                    $ardDate,
                    $dueDate,
                    $lastFiledAt,
                    $today
                );
            } else {
                [$status, $risk, $completed, $notes] = $this->assessEventBasedObligation(
                    $code,
                    $dueDate,
                    $lastFiledAt,
                    $today,
                    $officerChangeEventDate
                );
            }

            if ($completed && $lastFiledAt) {
                $completedAt = $lastFiledAt->toDateTimeString();
            }

            $company->croDocDefinitions()->updateExistingPivot($definition->id, [
                'completed' => $completed,
                'completed_at' => $completedAt,
                'completed_by' => null,
                'due_date' => $dueDate?->toDateString(),
                'last_filed_at' => $lastFiledAt?->toDateString(),
                'status' => $status,
                'risk_level' => $risk,
                'notes' => $notes,
            ]);
        }
    }

    protected function mapDetailsToAttributes(array $details): array
    {
        return [
            'name' => $this->uppercase($details['company_name'] ?? null),
            'company_type' => $details['comp_type_desc'] ?? null,
            'status' => $details['company_status_desc'] ?? null,
            'effective_date' => $this->formatCroDate($details['company_status_date'] ?? null),
            'registration_date' => $this->formatCroDate($details['company_reg_date'] ?? null),
            'last_annual_return' => $this->formatCroDate($details['last_ar_date'] ?? null),
            'next_annual_return' => $this->formatCroDate($details['next_ar_date'] ?? null),
            'next_financial_statement_due' => $this->formatCroDate($details['next_fs_due_date'] ?? null),
            'last_accounts' => $this->formatCroDate($details['last_acc_date'] ?? null),
            'last_agm' => $this->formatCroDate($details['last_agm_date'] ?? null),
            'financial_year_end' => $this->formatCroDate($details['financial_year_end'] ?? null),
            'postcode' => $this->uppercase($details['eircode'] ?? null),
            'address_line_1' => $this->uppercase($details['company_addr_1'] ?? null),
            'address_line_2' => $this->uppercase($details['company_addr_2'] ?? null),
            'address_line_3' => $this->uppercase($details['company_addr_3'] ?? null),
            'address_line_4' => $this->uppercase($details['company_addr_4'] ?? null),
            'place_of_business' => $this->uppercase($details['place_of_business'] ?? null),
            'company_type_code' => $details['company_type_code'] ?? null,
            'company_status_code' => $details['company_status_code'] ?? null,
        ];
    }

    protected function formatCroDate(?string $isoDate): ?string
    {
        if (!$isoDate || $isoDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return Carbon::parse($isoDate)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    protected function formatCroDateTime(?string $isoDate): ?string
    {
        if (!$isoDate || $isoDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return Carbon::parse($isoDate)->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return null;
        }
    }

    protected function uppercase(?string $value): ?string
    {
        return $value === null ? null : strtoupper($value);
    }

    protected function isValidCompanyNumber(?string $number): bool
    {
        if ($number === null) {
            return false;
        }

        return (bool) preg_match('/^\d{5,6}$/', $number);
    }

    protected function isListArray(array $value): bool
    {
        return function_exists('array_is_list') ? array_is_list($value) : array_keys($value) === range(0, count($value) - 1);
    }

    protected function ensureDefaultDefinitions(?int $businessId)
    {
        $defaults = [
            [
                'code' => 'B1',
                'name' => 'Annual Return',
                'description' => 'Must be filed within 56 days of the Annual Return Date (ARD).',
                'days_from_ard' => 56,
            ],
            [
                'code' => 'B10',
                'name' => 'Officer Change',
                'description' => 'Officer changes should be filed promptly (typically within 14 days).',
                'days_from_ard' => 14,
            ],
        ];

        foreach ($defaults as $default) {
            CroDocDefinition::firstOrCreate(
                ['code' => $default['code']],
                [
                    'name' => $default['name'],
                    'description' => $default['description'],
                    'days_from_ard' => $default['days_from_ard'],
                    'is_global' => true,
                    'business_id' => null,
                ]
            );
        }

        return CroDocDefinition::accessible((int) $businessId)
            ->whereIn('code', ['B1', 'B10'])
            ->get();
    }

    /**
     * @param array<int, array<string, mixed>> $submissions
     * @return array<string, array<string, mixed>>
     */
    protected function latestSubmissionByCode(array $submissions): array
    {
        $latest = [];

        foreach ($submissions as $submission) {
            $code = $this->extractFormCode($submission);

            if ($code === null) {
                continue;
            }

            $date = $this->extractSubmissionDate($submission);

            if (!$date) {
                continue;
            }

            if (!isset($latest[$code])) {
                $latest[$code] = $submission;
                continue;
            }

            $existingDate = $this->extractSubmissionDate($latest[$code]);

            if ($existingDate === null || $date->greaterThan($existingDate)) {
                $latest[$code] = $submission;
            }
        }

        return $latest;
    }

    /**
     * @param array<string, mixed> $submission
     */
    protected function extractFormCode(array $submission): ?string
    {
        $label = trim((string) (($submission['sub_type_desc'] ?? '') . ' ' . ($submission['doc_type_desc'] ?? '')));

        if ($label === '') {
            return null;
        }

        if (!preg_match('/\b([A-Z][0-9]{1,2}[A-Z]?)\b/i', $label, $matches)) {
            return null;
        }

        return strtoupper($matches[1]);
    }

    /**
     * @param array<string, mixed> $submission
     */
    protected function extractSubmissionDate(array $submission): ?Carbon
    {
        $rawDate = $submission['sub_received_date'] ?? $submission['sub_effective_date'] ?? null;

        if (!$rawDate || $rawDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return Carbon::parse($rawDate)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }

    protected function latestOfficerChangeEventDate(array $officers): ?Carbon
    {
        $latest = null;

        foreach ($officers as $officer) {
            if (!is_array($officer)) {
                continue;
            }

            foreach (['appointed_at', 'ceased_at'] as $key) {
                $date = $officer[$key] ?? null;

                if (!$date) {
                    continue;
                }

                try {
                    $parsed = Carbon::parse((string) $date)->startOfDay();
                } catch (Throwable) {
                    continue;
                }

                if ($latest === null || $parsed->greaterThan($latest)) {
                    $latest = $parsed;
                }
            }
        }

        return $latest;
    }

    protected function calculateDueDate(string $code, int $daysFromArd, ?Carbon $ardDate, ?Carbon $officerChangeEventDate): ?Carbon
    {
        if ($code === 'B1') {
            return $ardDate?->copy()->addDays(max(0, $daysFromArd));
        }

        if ($code === 'B10') {
            return $officerChangeEventDate?->copy()->addDays(14);
        }

        return $ardDate?->copy()->addDays(max(0, $daysFromArd));
    }

    /**
     * @return array{string, string, bool, ?string}
     */
    protected function assessAnnualReturn(?Carbon $ardDate, ?Carbon $dueDate, ?Carbon $lastFiledAt, Carbon $today): array
    {
        if ($ardDate === null || $dueDate === null) {
            return ['risky', 'high', false, 'Missing ARD from CRO; unable to calculate B1 deadline.'];
        }

        if ($lastFiledAt !== null && $lastFiledAt->greaterThanOrEqualTo($ardDate)) {
            return ['completed', 'low', true, 'Latest B1 filing appears to satisfy the current ARD cycle.'];
        }

        $daysRemaining = $today->diffInDays($dueDate, false);

        if ($daysRemaining < 0) {
            return ['overdue', 'high', false, 'B1 deadline has passed with no filing for the current ARD cycle.'];
        }

        if ($daysRemaining <= 14) {
            return ['due_soon', 'high', false, 'B1 deadline is due within 14 days.'];
        }

        return ['missing', 'medium', false, 'B1 filing has not yet been detected for the current ARD cycle.'];
    }

    /**
     * @return array{string, string, bool, ?string}
     */
    protected function assessEventBasedObligation(
        string $code,
        ?Carbon $dueDate,
        ?Carbon $lastFiledAt,
        Carbon $today,
        ?Carbon $officerChangeEventDate
    ): array {
        if ($code === 'B10' && $officerChangeEventDate === null) {
            if ($lastFiledAt !== null) {
                return ['completed', 'low', true, 'Most recent officer-change filing is recorded.'];
            }

            return ['risky', 'medium', false, 'No officer change event was found in CRO detail payload.'];
        }

        if ($dueDate === null) {
            return ['risky', 'medium', false, 'Deadline could not be derived from CRO payload.'];
        }

        if ($lastFiledAt !== null && $lastFiledAt->greaterThanOrEqualTo($dueDate->copy()->subDays(14))) {
            return ['completed', 'low', true, 'Relevant filing was detected near the expected deadline.'];
        }

        $daysRemaining = $today->diffInDays($dueDate, false);

        if ($daysRemaining < 0) {
            return ['overdue', 'high', false, 'No corresponding filing was detected before the deadline.'];
        }

        if ($daysRemaining <= 7) {
            return ['due_soon', 'high', false, 'Deadline is due within 7 days.'];
        }

        return ['missing', 'medium', false, 'Filing not yet detected for this obligation.'];
    }
}
