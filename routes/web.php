<?php

use App\Livewire\Cro\CroDocDefinitionManager;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\NotificationPreferences;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\SystemUpdate;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/notification', NotificationPreferences::class)->name('settings.notification');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/update', SystemUpdate::class)->name('settings.update');

    Route::get('cro-definitions', CroDocDefinitionManager::class)->name('cro-definitions');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/business.php';
require __DIR__ . '/company.php';
require __DIR__ . '/tag.php';
require __DIR__ . '/team.php';
