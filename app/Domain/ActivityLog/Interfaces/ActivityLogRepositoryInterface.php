<?php

declare(strict_types=1);

namespace App\Domain\ActivityLog\Interfaces;

use App\Domain\ActivityLog\Dto\StoreActivityLogDto;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;

interface ActivityLogRepositoryInterface
{
    public function getByDays(int $days = 14): Collection;

    public function create(StoreActivityLogDto $data): ActivityLog;
}
