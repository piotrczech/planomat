<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Consultation\Presentation\Http\Controllers\DeanOffice\ConsultationExportController;
use Modules\Consultation\Presentation\Http\Controllers\ScientificWorkerConsultationController;

Route::middleware(['auth', 'verified'])->prefix('consultations')->group(function (): void {

    // Scientific worker
    Route::group(['prefix' => 'scientific-worker'], function (): void {
        Route::get('', [ScientificWorkerConsultationController::class, 'index'])
            ->name('consultations.scientific-worker.my-consultation');
        Route::get('semester-consultation', [ScientificWorkerConsultationController::class, 'semesterConsultationIndex'])
            ->name('consultations.scientific-worker.my-semester-consultation');
        Route::get('session-consultation', [ScientificWorkerConsultationController::class, 'sessionConsultationIndex'])
            ->name('consultations.scientific-worker.my-session-consultation');
    });

    // Dean office worker
    Route::group(['prefix' => 'dean-office'], function (): void {
        Route::get('export/all/pdf/{semester}/{type}', [ConsultationExportController::class, 'downloadAllPdf'])
            ->name('consultations.dean-office.export.all-consultations.pdf');
        Route::get('export/unfilled/pdf/{semester}/{type}', [ConsultationExportController::class, 'downloadUnfilledPdf'])
            ->name('consultations.dean-office.export.unfilled-consultations.pdf');
    });
});
