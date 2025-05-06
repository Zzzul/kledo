<?php

namespace App\Providers;

use App\Services\StatusService;
use App\Repositories\StatusRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\StatusServiceInterface;
use App\Interfaces\StatusRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(abstract: StatusRepositoryInterface::class, concrete: StatusRepository::class);
        $this->app->bind(abstract: StatusServiceInterface::class, concrete: StatusService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
