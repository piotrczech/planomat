<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateOrCreateDesideratumDto extends Data
{
    public function __construct(
        public readonly bool|null|Optional $wantStationary,
        public readonly bool|null|Optional $wantNonStationary,
        public readonly bool|null|Optional $agreeToOvertime,
        public readonly array|null|Optional $unwantedCourseIds,
        public readonly array|null|Optional $wantedCourseIds,
        public readonly array|null|Optional $proficientCourseIds,
        public readonly int|null|Optional $masterThesesCount,
        public readonly int|null|Optional $bachelorThesesCount,
        public readonly int|null|Optional $maxHoursPerDay,
        public readonly int|null|Optional $maxConsecutiveHours,
        public readonly array|null|Optional $unavailableTimeSlots,
        public readonly string|null|Optional $additionalNotes,
    ) {
    }
}
