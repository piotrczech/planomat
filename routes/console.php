<?php

declare(strict_types=1);

use App\Application\Jobs\SendWeeklySummaryJob;
use App\Application\UseCases\Notifications\GenerateWeeklySummaryUseCase;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

// Summary -> every morning at 7:00
Schedule::call(function (): void {
    $useCase = app(GenerateWeeklySummaryUseCase::class);
    $summary = $useCase->execute();

    if ($summary) {
        SendWeeklySummaryJob::dispatch($summary);
    }
})->weeklyOn(1, '07:00')->name('weekly-summary');

Artisan::command('mail:send-weekly-summary', function (): void {
    $this->info('Generating and sending weekly summary...');

    $useCase = app(GenerateWeeklySummaryUseCase::class);
    $summary = $useCase->execute();

    if ($summary) {
        SendWeeklySummaryJob::dispatch($summary);
        $this->info('Weekly summary sent successfully.');
    } else {
        $this->warn('Weekly summary generation was skipped based on settings. No summary sent.');
    }
})->purpose('Send a test weekly summary email.');
