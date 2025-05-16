<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Desiderata\Presentation\Http\Controllers\ScientificWorkerDesiderataController;

Route::middleware(['auth', 'verified'])->prefix('desiderata')->group(function (): void {

    // Scientific worker
    Route::group(['prefix' => 'scientific-worker'], function (): void {
        Route::get('/', [ScientificWorkerDesiderataController::class, 'index'])
            ->name('desiderata.scientific-worker.my-desiderata');
    });

    // Dean office worker
});
