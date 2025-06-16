<?php

use App\Livewire\Company\Company;
use App\Livewire\Company\CompanyCreate;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyImport;
use App\Livewire\Company\CompanyManage;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/company', Company::class)->name('company.index');
    Route::get('/company/manage', CompanyManage::class)->name('company.manage');
    Route::get('/company/create', CompanyCreate::class)->name('company.create');
    Route::get('/company/{company}/edit', CompanyEdit::class)->name('company.edit');
    Route::get('/company/import', CompanyImport::class)->name('company.import');

    Route::get('/company/service/category/manager/', \App\Livewire\Company\Service\CategoryManager::class)->name('company.service.category.manager');
    Route::get('/company/service/provider/manager/', \App\Livewire\Company\Service\Provider\ProviderManager::class)->name('company.service.provider.manager');
    Route::get('/company/service/contract/manager/', \App\Livewire\Company\Service\ContractManager::class)->name('company.service.contract.manager');

    Route::get('/company/budget/forecast', \App\Livewire\Company\BudgetForecast::class)->name('company.budget.forecast');
    Route::get('/company/budget/calendar', \App\Livewire\Company\BudgetCalendar::class)->name('company.budget.calendar');
});
