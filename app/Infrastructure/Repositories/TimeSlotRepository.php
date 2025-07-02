<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\TimeSlotRepositoryInterface;
use App\Infrastructure\Models\TimeSlot;
use Illuminate\Support\Collection;

final readonly class TimeSlotRepository implements TimeSlotRepositoryInterface
{
    public function all(): Collection
    {
        return TimeSlot::orderBy('id')->get();
    }
}
