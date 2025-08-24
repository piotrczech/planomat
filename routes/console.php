<?php

declare(strict_types=1);

use App\Application\Jobs\SendWeeklySummaryJob;
use App\Application\UseCases\Notifications\GenerateWeeklySummaryUseCase;
use App\Application\UseCases\ActivityLog\PruneActivityLogsUseCase;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

Schedule::call(function (): void {
    $useCase = app(GenerateWeeklySummaryUseCase::class);
    $summary = $useCase->execute();

    if ($summary) {
        SendWeeklySummaryJob::dispatch($summary);
    }
})->weeklyOn(1, '07:00')->name('weekly-summary');

Schedule::call(function (): void {
    app(PruneActivityLogsUseCase::class)->execute(14);
})->dailyAt('03:00')->name('activity-logs-prune');

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

Artisan::command('maintenance:prune-activity-logs {days=14}', function (): void {
    $days = (int) $this->argument('days');
    $deleted = app(PruneActivityLogsUseCase::class)->execute($days);
    $this->info("Deleted {$deleted} activity log records older than {$days} days.");
})->purpose('Prune old activity logs.');
