<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Dto;

use Spatie\LaravelData\Data;

class CreateNewSemesterConsultationDto extends Data
{
    public function __construct(
        public string $consultationWeekday,
        public string $dailyConsultationWeekType,
        public string $weeklyConsultationDates,
        public string $consultationStartTime,
        public string $consultationEndTime,
        public string $consultationLocation,
    ) {
    }
}
