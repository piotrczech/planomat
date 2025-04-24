<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Desideratum\Http\Controllers\DesideratumController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::resource('desideratum', DesideratumController::class)->names('desideratum');
});
