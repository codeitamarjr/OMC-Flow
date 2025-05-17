<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Services\Core\CroSearchService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class CompanyFetchCroSubmissions
 *
 * This job fetches the CRO submissions for a given company and updates the submission documents.
 * It checks if the submissions have already been updated today, and if not, it fetches the latest
 * submissions from the CRO API and updates the company's submission documents accordingly.
 * 
 * @property-read Company $company The company for which to fetch the CRO submissions.
 * @method void __construct(Company $company) Creates a new instance of the job to fetch the CRO submissions for the given company.
 */
class CompanyFetchCroSubmissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Company $company;

    /**
     * Creates a new instance of the job to fetch the CRO submissions for the given company.
     *
     * @param Company $company The company to fetch the submissions for.
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * Fetches the CRO submissions for a company and updates the company's submission documents.
     * If the submissions have been updated today, then the job will not update the submission documents.
     * The job will update the submission documents if they have not been updated in the current day.
     *
     * @param CroSearchService $cro
     */
    public function handle(CroSearchService $cro): void
    {
        if (
            $this->company->submissionDocuments()->exists() &&
            optional($this->company->submissionDocuments()->latest()->first())->updated_at?->isToday()
        ) {
            Log::info('Submissions already updated today for company: ' . $this->company->company_number);
            return;
        }

        $submissions = $cro->searchCompanySubmissions($this->company->company_number, 'C');

        foreach ($submissions as $doc) {
            $this->company->submissionDocuments()->updateOrCreate([
                'sub_num' => $doc['sub_num'],
                'doc_num' => $doc['doc_num'],
            ], [
                'sub_type_desc' => $doc['sub_type_desc'] ?? null,
                'doc_type_desc' => $doc['doc_type_desc'] ?? null,
                'sub_status_desc' => $doc['sub_status_desc'] ?? null,
                'sub_received_date' => $this->formatDate($doc['sub_received_date'] ?? null),
                'sub_effective_date' => $this->formatDate($doc['sub_effective_date'] ?? null),
                'acc_year_to_date' => $this->formatDate($doc['acc_year_to_date'] ?? null),
                'scan_date' => $this->formatDate($doc['scan_date'] ?? null),
                'num_pages' => $doc['num_pages'] ?? null,
                'doc_id' => $doc['doc_id'] ?? null,
                'file_size' => $doc['file_size_bytes'] ?? null,
                'scanned' => isset($doc['scanned']) ? $doc['scanned'] === 'Y' : false,
            ]);
        }

        Log::info('Submissions updated for company: ' . $this->company->company_number);
    }


    /**
     * Converts an ISO date string to a 'Y-m-d' format.
     *
     * Returns null if the input is empty or '0001-01-01T00:00:00Z' (the default value returned by the CRO API).
     *
     * @param string|null $isoDate The ISO date string.
     * @return string|null The formatted date string.
     */
    protected function formatDate(?string $isoDate): ?string
    {
        if (!$isoDate || $isoDate === '0001-01-01T00:00:00Z') {
            return null;
        }

        try {
            return Carbon::parse($isoDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
