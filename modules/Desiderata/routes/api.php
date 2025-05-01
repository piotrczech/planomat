<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Desiderata\Http\Controllers\DesiderataController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (): void {
    Route::apiResource('desiderata', DesiderataController::class)->names('desiderata');
});
