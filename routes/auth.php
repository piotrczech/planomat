<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\Auth\UsosAuthController;

Route::middleware('guest')->group(function (): void {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Route::get('auth/usos', [UsosAuthController::class, 'redirect'])->name('usos.login');
    Route::get('auth/usos/redirect', [UsosAuthController::class, 'callback'])->name('usos.callback');
});

Route::middleware('auth')->group(function (): void {
    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});

Route::post('logout', App\Presentation\Livewire\Actions\Logout::class)
    ->name('logout');
