<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Spatie\LaravelData\Data;

final class ScientificWorkerActionStatusDto extends Data
{
    public function __construct(
        public readonly bool $showDesiderata = false,
        public readonly int $desiderataDueDays = 0,
        public readonly bool $showSemesterConsultations = false,
        public readonly int $semesterActiveForDays = 0,
        public readonly bool $showSessionConsultations = false,
        public readonly int $sessionConsultationsDueDays = 0,
        public readonly bool $anyActionsAvailable = false,
    ) {
    }
}
