<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Consultation\Http\Controllers\ConsultationController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::resource('consultation', ConsultationController::class)->names('consultation');
});
