<?php

namespace App\Providers;

use App\Interfaces\Approvals\ApprovalRepositoryInterface;
use App\Interfaces\Approvals\ApprovalServiceInterface;
use App\Interfaces\ApprovalStages\ApprovalStageRepositoryInterface;
use App\Interfaces\ApprovalStages\ApprovalStageServiceInterface;
use App\Interfaces\ApproverRepositoryInterface;
use App\Interfaces\ApproverServiceInterface;
use App\Interfaces\Expenses\ExpenseRepositoryInterface;
use App\Interfaces\Expenses\ExpenseServiceInterface;
use App\Interfaces\StatusRepositoryInterface;
use App\Interfaces\StatusServiceInterface;
use App\Repositories\ApprovalRepository;
use App\Repositories\ApprovalStageRepository;
use App\Repositories\ApproverRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\StatusRepository;
use App\Services\ApprovalService;
use App\Services\ApprovalStageService;
use App\Services\ApproverService;
use App\Services\ExpenseService;
use App\Services\StatusService;
use Illuminate\Support\ServiceProvider;

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
