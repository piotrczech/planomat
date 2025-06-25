<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Interfaces\Repositories;

use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Illuminate\Database\Eloquent\Collection;
use Modules\Consultation\Domain\Enums\ConsultationType;

interface ConsultationRepositoryInterface
{
    public function fetchAllForPdfExportBySemesterAndType(int $semesterId, ConsultationType $type): Collection;

    /**
     * Creates
     */
    public function createNewSemesterConsultation(CreateNewSemesterConsultationDto $dto): int;

    public function createNewSessionConsultation(CreateNewSessionConsultationDto $dto): int;

    // public function createPartTimeConsultation(): PartTimeConsultation;

    /**
     * Gets
     */
    public function getSemesterConsultations(int $scientificWorkerId, int $semesterId): array;

    public function getSessionConsultations(int $scientificWorkerId, int $semesterId): array;

    // public function getPartTimeConsultations(int $scientificWorkerId, int $semesterId): Collection;

    /**
     * Deletes
     */
    public function deleteSemesterConsultation(int $consultationId): bool;

    public function deleteSessionConsultation(int $consultationId): bool;

    // public function deletePartTimeConsultation(int $consultationId): bool;

    public function getLastUpdateDateForSemesterConsultation(int $scientificWorkerId): ?string;

    public function getLastUpdateDateForSessionConsultation(int $scientificWorkerId): ?string;

    // public function getLastPartTimeConsultationUpdateDate(int $scientificWorkerId): ?string;

    public function getConsultationSummaryTime(int $scientificWorkerId): ?string;

    public function getAllScientificWorkersWithConsultations(int $semesterId, ConsultationType $type): Collection;

    public function getScientificWorkersWithoutConsultations(int $semesterId, ConsultationType $type): Collection;
}
