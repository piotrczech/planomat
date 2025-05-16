<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Interfaces\Repositories;

use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;

interface ConsultationRepositoryInterface
{
    public function createWeekdayConsultation(
        int $scientificWorkerId,
        int $semesterId,
        CreateNewSemesterConsultationDto $dto,
    ): bool;

    public function createWeekendConsultations(
        int $scientificWorkerId,
        int $semesterId,
        CreateNewSemesterConsultationDto $dto,
    ): int;

    public function getSemesterConsultations(int $scientificWorkerId, int $semesterId): array;

    public function deleteSemesterConsultation(int $consultationId, int $scientificWorkerId): bool;

    public function createSessionConsultation(
        int $scientificWorkerId,
        CreateNewSessionConsultationDto $dto,
    ): int;

    public function getSessionConsultations(int $scientificWorkerId): array;

    public function deleteSessionConsultation(int $consultationId, int $scientificWorkerId): bool;

    public function getLastSemesterConsultationUpdateDate(int $scientificWorkerId): ?string;

    public function getLastSessionConsultationUpdateDate(int $scientificWorkerId): ?string;
}
