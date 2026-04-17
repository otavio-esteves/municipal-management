<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use App\Policies\CategoryPolicy;
use App\Policies\SecretariatPolicy;
use App\Policies\ServiceOrderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Secretariat::class, SecretariatPolicy::class);
        Gate::policy(ServiceOrder::class, ServiceOrderPolicy::class);
    }
}
