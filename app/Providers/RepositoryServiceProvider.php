<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\ManagerAreaRepositoryInterface;
use App\Repositories\Contracts\OkrRepositoryInterface;
use App\Repositories\Contracts\SubmissionCommentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use App\Repositories\Eloquent\ManagerAreaRepository;
use App\Repositories\Eloquent\OkrRepository;
use App\Repositories\Eloquent\SubmissionCommentRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WeeklySubmissionRepository;
use App\Services\External\Contracts\AzureAdServiceInterface;
use App\Services\External\Implementation\AzureAdService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            AzureAdServiceInterface::class,
            AzureAdService::class,
        );

        $this->app->bind(
            WeeklySubmissionRepositoryInterface::class,
            WeeklySubmissionRepository::class,
        );

        $this->app->bind(
            OkrRepositoryInterface::class,
            OkrRepository::class,
        );

        $this->app->bind(
            ManagerAreaRepositoryInterface::class,
            ManagerAreaRepository::class,
        );

        $this->app->bind(
            SubmissionCommentRepositoryInterface::class,
            SubmissionCommentRepository::class,
        );
    }
}
