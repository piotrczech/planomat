<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\DashboardController;
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
    Route::view('admin-dean-dashboard', 'dashboards.admin-dean')->name('admin-dean-dashboard'); // TODO: add middleware for admin role
    Route::view('scientific-worker-dashboard', 'dashboards.scientific-worker')->name('scientific-worker-dashboard'); // TODO: add middleware for scientific worker role

    Route::middleware(['auth', 'verified'])
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
