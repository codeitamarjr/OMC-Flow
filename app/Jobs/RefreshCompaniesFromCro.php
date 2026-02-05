<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->onQueue(config('services.cro.queue', 'default'));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Company::query()
            ->whereNotNull('company_number')
            ->whereRaw("company_number REGEXP '^[0-9]{5,6}$'")
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($companies) {
                foreach ($companies as $company) {
                    RefreshSingleCompanyFromCro::dispatch($company->id);
                }
            });
    }
}
