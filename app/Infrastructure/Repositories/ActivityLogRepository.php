<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\ActivityLogRepositoryInterface;
use App\Domain\Dto\StoreActivityLogDto;
use App\Infrastructure\Models\ActivityLog;
use Illuminate\Support\Collection;
use Carbon\Carbon;

final class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function getByDays(int $days = 14): Collection
    {
        return ActivityLog::with('user')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(StoreActivityLogDto $data): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $data->userId,
            'module' => $data->module,
            'action' => $data->action,
        ]);
    }
}
