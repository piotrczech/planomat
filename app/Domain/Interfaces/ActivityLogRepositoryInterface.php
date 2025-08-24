<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Dto\StoreActivityLogDto;
use App\Infrastructure\Models\ActivityLog;
use Illuminate\Support\Collection;

interface ActivityLogRepositoryInterface
{
    public function getByDays(int $days = 14): Collection;

    public function create(StoreActivityLogDto $data): ActivityLog;

    public function deleteOlderThanDays(int $days = 14): int;
}
