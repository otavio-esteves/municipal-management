<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\SecretariatManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Secretariat\ServiceOrderManager;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->secretariat_id) {
            return redirect()->route('secretariats.ods', ['secretariat' => $user->secretariat_id]);
        }

        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Rotas Administrativas - Fase 2
    Route::get('/admin/secretarias', SecretariatManager::class)->name('admin.secretariats');
    Route::get('/admin/categorias', CategoryManager::class)->name('admin.categories');
    Route::get('/secretarias/{secretariat}/ods', ServiceOrderManager::class)->name('secretariats.ods');
});

require __DIR__.'/auth.php';