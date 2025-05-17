<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
        Route::get('/team/manager', \App\Livewire\Team\TeamManager::class)->name('team.manager');
});
