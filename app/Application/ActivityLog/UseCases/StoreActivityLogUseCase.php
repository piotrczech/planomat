<?php

declare(strict_types=1);

namespace App\Application\ActivityLog\UseCases;

use App\Domain\ActivityLog\Interfaces\ActivityLogRepositoryInterface;
use App\Domain\ActivityLog\Dto\StoreActivityLogDto;
use App\Models\ActivityLog;

final readonly class StoreActivityLogUseCase
{
    public function __construct(
        private ActivityLogRepositoryInterface $activityLogRepository,
    ) {
    }

    public function execute(StoreActivityLogDto $data): ActivityLog
    {
        return $this->activityLogRepository->create($data);
    }
}
