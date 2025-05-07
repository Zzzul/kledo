<?php

namespace App\Providers;

use App\Services\StatusService;
use App\Services\ExpenseService;
use App\Services\ApprovalService;
use App\Services\ApproverService;
use App\Repositories\StatusRepository;
use App\Services\ApprovalStageService;
use App\Repositories\ExpenseRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ApprovalRepository;
use App\Repositories\ApproverRepository;
use App\Interfaces\StatusServiceInterface;
use App\Interfaces\ApproverServiceInterface;
use App\Interfaces\StatusRepositoryInterface;
use App\Repositories\ApprovalStageRepository;
use App\Interfaces\ApproverRepositoryInterface;
use App\Interfaces\Expenses\ExpenseServiceInterface;
use App\Interfaces\Approvals\ApprovalServiceInterface;
use App\Interfaces\Expenses\ExpenseRepositoryInterface;
use App\Interfaces\Approvals\ApprovalRepositoryInterface;
use App\Interfaces\ApprovalStages\ApprovalStageServiceInterface;
use App\Interfaces\ApprovalStages\ApprovalStageRepositoryInterface;

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

        $this->app->bind(abstract: ApprovalStageRepositoryInterface::class, concrete: ApprovalStageRepository::class);
        $this->app->bind(abstract: ApprovalStageServiceInterface::class, concrete: ApprovalStageService::class);

        $this->app->bind(abstract: ExpenseRepositoryInterface::class, concrete: ExpenseRepository::class);
        $this->app->bind(abstract: ExpenseServiceInterface::class, concrete: ExpenseService::class);

        $this->app->bind(abstract: ApprovalRepositoryInterface::class, concrete: ApprovalRepository::class);
        $this->app->bind(abstract: ApprovalServiceInterface::class, concrete: ApprovalService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
