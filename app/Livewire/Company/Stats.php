<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Stats extends Component
{
    /**
     * @var array<int, string>
     */
    protected array $coreCroCodes = ['B1', 'B10', 'B2', 'AGM'];

    public function render()
    {
        $businessId = Auth::user()->current_business_id;

        $companies = Company::query()
            ->where('business_id', $businessId)
            ->where(function ($q) {
                $q->where('active', true)->orWhereNull('active');
            });

        $totalCompanies = (clone $companies)->count();
        $lastSyncedAt = (clone $companies)->max('cro_officers_synced_at');
        $overdueAr = (clone $companies)
            ->whereNotNull('next_annual_return')
            ->whereDate('next_annual_return', '<', now()->toDateString())
            ->count();
        $dueSoonAr = (clone $companies)
            ->whereNotNull('next_annual_return')
            ->whereBetween('next_annual_return', [now()->toDateString(), now()->addDays(14)->toDateString()])
            ->count();

        $riskRows = DB::table('company_cro_document')
            ->join('companies', 'companies.id', '=', 'company_cro_document.company_id')
            ->join('cro_doc_definitions', 'cro_doc_definitions.id', '=', 'company_cro_document.cro_doc_definition_id')
            ->where('companies.business_id', $businessId)
            ->where(function ($q) {
                $q->where('companies.active', true)->orWhereNull('companies.active');
            })
            ->whereIn('cro_doc_definitions.code', $this->coreCroCodes)
            ->selectRaw("
                SUM(CASE WHEN company_cro_document.status = 'overdue' THEN 1 ELSE 0 END) as overdue_count,
                SUM(CASE WHEN company_cro_document.status = 'risky' THEN 1 ELSE 0 END) as risky_count,
                SUM(CASE WHEN company_cro_document.status = 'missing' THEN 1 ELSE 0 END) as missing_count,
                SUM(CASE WHEN company_cro_document.status = 'completed' THEN 1 ELSE 0 END) as completed_count
            ")
            ->first();

        return view('livewire.company.stats', [
            'totalCompanies' => $totalCompanies,
            'overdueAr' => (int) ($riskRows->overdue_count ?? $overdueAr),
            'dueSoonAr' => $dueSoonAr,
            'riskyCount' => (int) ($riskRows->risky_count ?? 0),
            'missingCount' => (int) ($riskRows->missing_count ?? 0),
            'completedCount' => (int) ($riskRows->completed_count ?? 0),
            'lastSyncedAt' => $lastSyncedAt ? Carbon::parse($lastSyncedAt) : null,
        ]);
    }
}
