<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Desiderata\Presentation\Http\Controllers\DeanOffice\DesiderataExportController;
use Modules\Desiderata\Presentation\Http\Controllers\ScientificWorkerDesiderataController;

Route::middleware(['auth', 'verified'])->prefix('desiderata')->group(function (): void {

    // Scientific worker
    Route::group(['prefix' => 'scientific-worker'], function (): void {
        Route::get('/', [ScientificWorkerDesiderataController::class, 'index'])
            ->name('desiderata.scientific-worker.my-desiderata');
    });

    // Dean office worker
    Route::group(['prefix' => 'dean-office'], function (): void {
        Route::get('export/all/pdf/{semester}', [DesiderataExportController::class, 'downloadAllPdf'])
            ->name('desiderata.dean-office.export.all-desiderata.pdf');

        Route::get('export/unfilled/pdf/{semester}', [DesiderataExportController::class, 'downloadUnfilledPdf'])
            ->name('desiderata.dean-office.export.unfilled-desiderata.pdf');
    });
});
