<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\Admin\SettingsController;
use App\Presentation\Http\Controllers\AdminDeanDashboardController;
use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\ScientificWorkerDashboardController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::impersonate();

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/appearance');

    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('admin-dean-dashboard', AdminDeanDashboardController::class)
        ->name('admin-dean-dashboard')
        ->middleware('admin.dean');

    Route::get('scientific-worker-dashboard', ScientificWorkerDashboardController::class)
        ->name('scientific-worker-dashboard')
        ->middleware('scientific.worker');

    Route::middleware(['admin.dean'])
        ->prefix('admin/settings')
        ->name('admin.settings.')
        ->controller(SettingsController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::prefix('general')->name('general.')->group(function (): void {
                Route::get('courses', 'manageCourses')->name('courses');
                Route::get('semesters', 'manageSemesters')->name('semesters');
                Route::get('users', 'manageUsers')->name('users');
            });
        });
});

require __DIR__.'/auth.php';
