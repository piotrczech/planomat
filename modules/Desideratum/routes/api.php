<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Desideratum\Http\Controllers\DesideratumController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (): void {
    Route::apiResource('desideratum', DesideratumController::class)->names('desideratum');
});
