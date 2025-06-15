<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Interfaces\ActivityLogRepositoryInterface;
use App\Domain\Interfaces\CourseRepositoryInterface;
use App\Infrastructure\Repositories\ActivityLogRepository;
use App\Infrastructure\Repositories\CourseRepository;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Repositories\SemesterRepository;
use App\Domain\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Repositories\UserRepository;
use App\Presentation\View\Composers\CurrentSemesterComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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

    public function boot(): void
    {
        Blade::component('App\\Presentation\\View\\Components\\UserLayout', 'user-layout');

        View::composer(['components.layouts.app.header', 'dashboards.*'], CurrentSemesterComposer::class);
    }
}
