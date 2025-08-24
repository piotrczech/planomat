<?php

declare(strict_types=1);

namespace App\Application\UseCases\ActivityLog;

use App\Domain\Interfaces\ActivityLogRepositoryInterface;

final readonly class PruneActivityLogsUseCase
{
    public function __construct(private ActivityLogRepositoryInterface $activityLogRepository)
    {
    }

    public function execute(int $days = 14): int
    {
        return $this->activityLogRepository->deleteOlderThanDays($days);
    }
}
