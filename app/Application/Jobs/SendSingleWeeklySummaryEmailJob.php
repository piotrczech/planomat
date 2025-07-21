<?php

declare(strict_types=1);

namespace App\Application\Jobs;

use App\Application\Mail\WeeklySummaryMail;
use App\Domain\Dto\WeeklySummaryDto;
use App\Infrastructure\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

final class SendSingleWeeklySummaryEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 300;

    public function __construct(
        private readonly User $recipient,
        private readonly WeeklySummaryDto $summaryDto,
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->recipient->email)
            ->send(new WeeklySummaryMail($this->summaryDto));
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new RateLimited('weekly-summary-emails')];
    }
}
