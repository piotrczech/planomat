<?php

declare(strict_types=1);

namespace App\Application\UseCases\ActivityLog;

use App\Domain\Interfaces\ActivityLogRepositoryInterface;
use App\Domain\Dto\StoreActivityLogDto;
use App\Infrastructure\Models\ActivityLog;

final readonly class CreateActivityLogUseCase
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
