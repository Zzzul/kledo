<?php

namespace App\Providers;

use App\Services\StatusService;
use App\Services\ApproverService;
use App\Repositories\StatusRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ApproverRepository;
use App\Interfaces\StatusServiceInterface;
use App\Interfaces\ApproverServiceInterface;
use App\Interfaces\StatusRepositoryInterface;
use App\Interfaces\ApproverRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(abstract: StatusRepositoryInterface::class, concrete: StatusRepository::class);
        $this->app->bind(abstract: StatusServiceInterface::class, concrete: StatusService::class);

        $this->app->bind(abstract: ApproverRepositoryInterface::class, concrete: ApproverRepository::class);
        $this->app->bind(abstract: ApproverServiceInterface::class, concrete: ApproverService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
