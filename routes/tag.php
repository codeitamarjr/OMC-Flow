<?php

use App\Livewire\Tag\TagCreate;
use App\Livewire\Tag\TagManager;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
        Route::get('/tags', TagManager::class)->name('tag.index');
        Route::get('/tags/create', TagCreate::class)->name('tag.create');
});
