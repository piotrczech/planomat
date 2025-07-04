<?php

declare(strict_types=1);

namespace App\Application\UseCases\ActivityLog;

use App\Domain\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Support\Collection;

final readonly class GetActivityLogsByLastDaysUseCase
{
    public function __construct(private ActivityLogRepositoryInterface $activityLogRepository)
    {
    }

    public function execute(int $days = 14): Collection
    {
        return $this->activityLogRepository->getByDays($days);
    }
}
