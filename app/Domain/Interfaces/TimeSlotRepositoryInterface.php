<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use Illuminate\Support\Collection;

interface TimeSlotRepositoryInterface
{
    public function all(): Collection;
}
