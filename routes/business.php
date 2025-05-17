<?php

use App\Livewire\Business\Business;
use Illuminate\Support\Facades\Route;
use App\Livewire\Business\BusinessCreate;


Route::middleware('auth')->group(function () {
    Route::get('/business', Business::class)->name('business.index');
    Route::get('/business/create', BusinessCreate::class)->name('business.create');
});
