<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Consultation\Http\Controllers\ConsultationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (): void {
    Route::apiResource('consultation', ConsultationController::class)->names('consultation');
});
