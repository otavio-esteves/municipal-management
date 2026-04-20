<?php

namespace App\Providers;

use App\Application\Categories\Contracts\CategoryRepository;
use App\Application\Secretariats\Contracts\SecretariatRepository;
use App\Application\ServiceOrders\Contracts\ServiceOrderRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentSecretariatRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentServiceOrderRepository;
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
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(SecretariatRepository::class, EloquentSecretariatRepository::class);
        $this->app->bind(ServiceOrderRepository::class, EloquentServiceOrderRepository::class);
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
