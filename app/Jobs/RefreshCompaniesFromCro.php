<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\Core\CroSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshCompaniesFromCro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $chunkSize The number of companies processed per chunk.
     */
    public function __construct(protected int $chunkSize = 100)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(CroSearchService $cro): void
    {
        Company::query()
            ->whereNotNull('company_number')
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($companies) use ($cro) {
                foreach ($companies as $company) {
                    if (!$this->isValidCompanyNumber($company->company_number)) {
                        continue;
                    }

                    try {
                        $details = $this->fetchCompanyDetails($cro, $company->company_number);

                        if ($details === null) {
                            continue;
                        }

                        $company->update($this->mapDetailsToAttributes($details));
                    } catch (Throwable $e) {
                        Log::warning('Failed refreshing company from CRO', [
                            'company_id' => $company->id,
                            'company_number' => $company->company_number,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            });
    }

    protected function fetchCompanyDetails(CroSearchService $cro, string $companyNumber): ?array
    {
        $details = $cro->getCompanyDetails($companyNumber);

        return empty($details) ? null : $details;
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
}
