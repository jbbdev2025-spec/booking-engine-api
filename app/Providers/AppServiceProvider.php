<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\BookingRepositoryInterface;
use App\Contracts\Repositories\CatalogRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\BookingRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\CatalogRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            BookingRepositoryInterface::class,
            BookingRepository::class
        );

        $this->app->bind(
            CatalogRepositoryInterface::class,
            CatalogRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
