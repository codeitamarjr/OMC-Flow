<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use App\Jobs\CompanyFetchCroSubmissions;

class RefreshCroSubmissions extends Command
{

    protected $signature = 'cro:refresh-submissions 
                            {--company= : Limit to a specific company ID}
                            {--force : Force refresh even if already updated today}';

    protected $description = 'Dispatch jobs to refresh CRO submission documents for companies';


    public function handle(): int
    {
        $companyId = $this->option('company');
        $force = $this->option('force');

        $query = Company::query();

        if ($companyId) {
            $query->where('id', $companyId);
        }

        $companies = $query->get();

        if ($companies->isEmpty()) {
            $this->warn('No companies found for submission refresh.');
            return Command::SUCCESS;
        }

        $this->info("Dispatching CRO submission refresh jobs for {$companies->count()} company(ies)...");

        foreach ($companies as $company) {
            if (
                ! $force && $company->submissionDocuments()->exists() &&
                optional($company->submissionDocuments()->latest('updated_at')->first())->updated_at?->isToday()
            ) {
                $this->line("⏭  Skipping {$company->name} (already updated today)");
                continue;
            }

            CompanyFetchCroSubmissions::dispatch($company);
            $this->line("🚀 Dispatched: {$company->name}");
        }

        $this->info('✅ All applicable jobs dispatched.');
        return Command::SUCCESS;
    }
}
