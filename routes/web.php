<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\SecretariatManager;
use App\Livewire\Admin\CategoryManager;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Rotas Administrativas - Fase 2
    Route::get('/admin/secretarias', SecretariatManager::class)->name('admin.secretariats');
    Route::get('/admin/categorias', CategoryManager::class)->name('admin.categories');
});

require __DIR__.'/auth.php';