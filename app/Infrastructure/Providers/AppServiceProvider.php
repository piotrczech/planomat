<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Enums\RoleEnum;
use App\Domain\Interfaces\ActivityLogRepositoryInterface;
use App\Domain\Interfaces\CourseRepositoryInterface;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Domain\Interfaces\TimeSlotRepositoryInterface;
use App\Domain\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Repositories\ActivityLogRepository;
use App\Infrastructure\Repositories\CourseRepository;
use App\Infrastructure\Repositories\SemesterRepository;
use App\Infrastructure\Repositories\SettingRepository;
use App\Infrastructure\Repositories\TimeSlotRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Presentation\View\Composers\CurrentSemesterComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Domain\Interfaces\AcademicTitleRepositoryInterface;
use App\Infrastructure\Repositories\AcademicTitleRepository;
use SocialiteProviders\Keycloak\Provider as KeycloakSocialiteProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use App\Domain\Interfaces\Services\PdfGeneratorInterface;
use App\Infrastructure\Services\DomPdfGenerator;
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            SemesterRepositoryInterface::class,
            SemesterRepository::class,
        );

        $this->app->bind(
            CourseRepositoryInterface::class,
            CourseRepository::class,
        );

        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            ActivityLogRepository::class,
        );

        $this->app->bind(
            SettingRepositoryInterface::class,
            SettingRepository::class,
        );

        $this->app->bind(
            TimeSlotRepositoryInterface::class,
            TimeSlotRepository::class,
        );

        $this->app->bind(
            AcademicTitleRepositoryInterface::class,
            AcademicTitleRepository::class,
        );

        $this->app->bind(
            PdfGeneratorInterface::class,
            DomPdfGenerator::class,
        );
    }

    public function boot(): void
    {
        Blade::component('App\\Presentation\\View\\Components\\UserLayout', 'user-layout');

        View::composer(['components.layouts.app.header', 'dashboards.*'], CurrentSemesterComposer::class);

        Event::listen(
            SocialiteWasCalled::class,
            static function (SocialiteWasCalled $event): void {
                $event->extendSocialite('keycloak', KeycloakSocialiteProvider::class);
            },
        );

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole(RoleEnum::ADMINISTRATOR);
        });

        Gate::define('manageDeanOffice', function (User $user) {
            return $user->hasRole(RoleEnum::ADMINISTRATOR);
        });

        RateLimiter::for('weekly-summary-emails', function (object $job) {
            return Limit::perMinute(10);
        });

        RateLimiter::for('reminder-emails', function (object $job) {
            return Limit::perMinutes(10, 5);
        });

        Health::checks([
            DatabaseCheck::new(),
            QueueCheck::new(),
            UsedDiskSpaceCheck::new()
                ->failWhenUsedSpaceIsAbovePercentage(90)
                ->daily(),
        ]);
    }
}
