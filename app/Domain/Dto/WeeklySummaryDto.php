<?php

declare(strict_types=1);

namespace App\Domain\Dto;

final readonly class WeeklySummaryDto
{
    public function __construct(
        public array $generalActivity,
        public iterable $consultationsActivity,
        public iterable $desiderataActivity,
        public string $weekStart,
        public string $weekEnd,
        public bool $isConsultationSemesterActive,
        public bool $isDesiderataSemesterActive,
    ) {
    }
}
