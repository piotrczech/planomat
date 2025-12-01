<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Consultation\Presentation\Http\Controllers\DeanOffice\ConsultationExportController;
use Modules\Consultation\Presentation\Http\Controllers\ScientificWorkerConsultationController;

Route::middleware(['auth', 'verified'])->prefix('consultations')->group(function (): void {

    Route::group(['prefix' => 'scientific-worker', 'middleware' => ['scientific.worker', 'require.semester']], function (): void {
        Route::get('', [ScientificWorkerConsultationController::class, 'index'])
            ->name('consultations.scientific-worker.my-consultation');
        Route::get('semester-consultation', [ScientificWorkerConsultationController::class, 'semesterConsultationIndex'])
            ->name('consultations.scientific-worker.my-semester-consultation');
        Route::get('session-consultation', [ScientificWorkerConsultationController::class, 'sessionConsultationIndex'])
            ->name('consultations.scientific-worker.my-session-consultation');
        Route::get('part-time-consultation', [ScientificWorkerConsultationController::class, 'partTimeConsultationIndex'])
            ->name('consultations.scientific-worker.my-part-time-consultation');
    });

    Route::group(['prefix' => 'dean-office', 'middleware' => 'admin.dean'], function (): void {
        Route::get('export/all-consultations/pdf', [ConsultationExportController::class, 'downloadAllPdf'])
            ->name('consultations.dean-office.export.all-consultations.pdf');

        Route::get('export/all-consultations/excel', [ConsultationExportController::class, 'downloadAllExcel'])
            ->name('consultations.dean-office.export.all-consultations.excel');

        Route::get('export/unfilled-consultations/pdf', [ConsultationExportController::class, 'downloadUnfilledPdf'])
            ->name('consultations.dean-office.export.unfilled-consultations.pdf');
    });
});
