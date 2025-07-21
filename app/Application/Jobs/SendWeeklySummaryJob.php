<?php

declare(strict_types=1);

namespace App\Application\Jobs;

use App\Domain\Dto\WeeklySummaryDto;
use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SendWeeklySummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly WeeklySummaryDto $summaryDto,
    ) {
    }

    public function handle(): void
    {
        $recipients = User::whereIn('role', [RoleEnum::ADMINISTRATOR, RoleEnum::DEAN_OFFICE_WORKER])
            ->whereNull('deleted_at')
            ->get();

        foreach ($recipients as $recipient) {
            SendSingleWeeklySummaryEmailJob::dispatch($recipient, $this->summaryDto);
        }
    }
}
