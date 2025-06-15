<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Interfaces\Repositories;

use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Illuminate\Database\Eloquent\Collection;
use Modules\Consultation\Domain\Enums\ConsultationType;

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
        int $semesterId,
        CreateNewSessionConsultationDto $dto,
    ): int;

    public function getSessionConsultations(int $scientificWorkerId, int $semesterId): array;

    public function deleteSessionConsultation(int $consultationId, int $scientificWorkerId): bool;

    public function getLastSemesterConsultationUpdateDate(int $scientificWorkerId): ?string;

    public function getLastSessionConsultationUpdateDate(int $scientificWorkerId): ?string;

    public function getConsultationSummaryTime(int $scientificWorkerId): ?string;

    public function fetchAllForPdfExportBySemesterAndType(int $semesterId, ConsultationType $type): Collection;

    public function getAllScientificWorkersWithConsultations(int $semesterId, ConsultationType $type): Collection;

    public function getScientificWorkersWithoutConsultations(int $semesterId, ConsultationType $type): Collection;
}
