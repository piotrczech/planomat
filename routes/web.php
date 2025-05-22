<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::view('admin-dean-dashboard', 'dashboards.admin-dean')->name('admin-dean-dashboard'); // TODO: add middleware for admin role
    Route::view('scientific-worker-dashboard', 'dashboards.scientific-worker')->name('scientific-worker-dashboard'); // TODO: add middleware for scientific worker role

    Route::controller(ProfileController::class)->group(function (): void {
        Route::get('profile', 'edit')->name('profile.edit');
        Route::patch('profile', 'update')->name('profile.update');
        Route::delete('profile', 'destroy')->name('profile.destroy');
    });

    // Admin Settings Panel Routes
    Route::middleware(['auth', 'verified' /* TODO: add 'role:admin' or similar middleware */])
        ->prefix('admin/settings')
        ->name('admin.settings.')
        ->controller(AdminSettingsController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::prefix('general')->name('general.')->group(function (): void {
                Route::get('courses', 'manageCourses')->name('courses');
                Route::get('semesters', 'manageSemesters')->name('semesters');
            });
            // TODO: Placeholder for module-specific admin settings routes if needed later
            // For Desiderata module:
            // Route::prefix('desiderata')->name('desiderata.')->group(function () {
            //     // Route::get('periods', 'manageDesiderataPeriods')->name('periods');
            // });
            // For Consultation module:
            // Route::prefix('consultation')->name('consultation.')->group(function () {
            //     // Route::get('configurations', 'manageConsultationConfigurations')->name('configurations');
            // });
        });
});

require __DIR__.'/auth.php';
