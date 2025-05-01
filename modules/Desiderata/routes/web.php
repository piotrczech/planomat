<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Desiderata\Http\Controllers\DesiderataController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::resource('desiderata', DesiderataController::class)->names('desiderata');
});
