<?php

declare(strict_types=1);

namespace App\Application\Jobs;

use App\Application\Mail\MissingDataNotificationMail;
use App\Domain\Dto\MissingDataNotificationDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class SendMissingDataNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 300;

    public function __construct(
        private readonly MissingDataNotificationDto $notificationDto,
    ) {
    }

    public function handle(): void
    {
        $now = now();
        $dto = $this->notificationDto;

        Log::info('[SendMissingDataNotificationJob] Sending email of type: ' . $dto->type . ' for user: ' . $this->notificationDto->user->email);

        if ($dto->type === 'desiderata') {
            $semester = $dto->semester;

            if (!$semester || !$now->greaterThanOrEqualTo($semester->semester_start_date) || !$now->lessThan($semester->end_date)) {
                Log::info('[SendMissingDataNotificationJob] email not sent - desiderata out of semester');

                return;
            }
        }

        if ($dto->type === 'consultation_semester') {
            $semester = $dto->semester;

            if (!$semester || !$now->greaterThanOrEqualTo($semester->semester_start_date) || !$now->lessThan($semester->session_start_date)) {
                Log::info('[SendMissingDataNotificationJob] email not sent - consultation semester out of semester');

                return;
            }
        }

        if ($dto->type === 'consultation_session') {
            $semester = $dto->semester;

            if (!$semester || !$now->greaterThanOrEqualTo($semester->session_start_date) || !$now->lessThanOrEqualTo($semester->end_date)) {
                Log::info('[SendMissingDataNotificationJob] email not sent - consultation session out of semester');

                return;
            }
        }

        Mail::to($this->notificationDto->user->email)
            ->send(new MissingDataNotificationMail($this->notificationDto));
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new RateLimited('reminder-emails')];
    }
}
