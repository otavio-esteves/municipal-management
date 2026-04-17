<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Secretariat;
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
    Route::get('/admin/secretarias', SecretariatManager::class)
        ->can('viewAny', Secretariat::class)
        ->name('admin.secretariats');
    Route::get('/admin/categorias', CategoryManager::class)
        ->can('viewAny', Category::class)
        ->name('admin.categories');
    Route::get('/secretarias/{secretariat}/ods', ServiceOrderManager::class)
        ->can('view', 'secretariat')
        ->name('secretariats.ods');
});

require __DIR__.'/auth.php';
