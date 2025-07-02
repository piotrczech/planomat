<?php

declare(strict_types=1);

namespace App\Application\UseCases\TimeSlot;

use App\Domain\Interfaces\TimeSlotRepositoryInterface;
use Illuminate\Support\Collection;

final readonly class GetAllTimeSlotsUseCase
{
    public function __construct(private TimeSlotRepositoryInterface $repository)
    {
    }

    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
