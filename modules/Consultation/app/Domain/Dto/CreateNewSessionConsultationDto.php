<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Dto;

use Spatie\LaravelData\Data;

final class CreateNewSessionConsultationDto extends Data
{
    public function __construct(
        public string $consultationDate,
        public string $consultationStartTime,
        public string $consultationEndTime,
        public string $consultationLocation,
    ) {
    }
}
