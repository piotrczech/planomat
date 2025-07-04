<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\Auth\AccountPendingController;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\Auth\UsosAuthController;
use App\Presentation\Livewire\Actions\Logout;

Route::middleware('guest')->group(function (): void {
    Volt::route('login', 'auth.login')
        ->name('login');

    Route::get('auth/usos', [UsosAuthController::class, 'redirect'])->name('usos.login');
    Route::get('auth/usos/redirect', [UsosAuthController::class, 'callback'])->name('usos.callback');

    Route::get('account/pending', AccountPendingController::class)
        ->name('account.pending');
});

Route::middleware('auth')->group(function (): void {
    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});

Route::post('logout', Logout::class)
    ->name('logout');
