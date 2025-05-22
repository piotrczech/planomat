<?php

declare(strict_types=1);

namespace Modules\Desiderata\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Collection;
use Modules\Desiderata\Domain\Repository\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

final class EloquentDesideratumRepository implements DesideratumRepositoryInterface
{
    public function getAllDesiderataForPdfExport(): Collection
    {
        $allDesiderata = Desideratum::with([
            'scientificWorker',
            'semester',
            'wantedCourses',
            'couldCourses',
            'notWantedCourses',
            'unavailableTimeSlots.timeSlot',
        ])->get();

        return $allDesiderata->sortBy(function (Desideratum $desideratum) {
            if ($desideratum->scientificWorker) {
                return mb_strtolower($desideratum->scientificWorker->surname ?? '') . ' ' . mb_strtolower($desideratum->scientificWorker->name ?? '');
            }

            return 'zzzzzzzz';
        })->values();
    }
}
