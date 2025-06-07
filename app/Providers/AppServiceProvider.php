<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\ActivityLog\Interfaces\ActivityLogRepositoryInterface;
use App\Domain\Course\Interfaces\CourseRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\ActivityLogRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\CourseRepository;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\SemesterRepository;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CourseRepositoryInterface::class,
            CourseRepository::class,
        );

        $this->app->bind(
            SemesterRepositoryInterface::class,
            SemesterRepository::class,
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            ActivityLogRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
