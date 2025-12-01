<?php

declare(strict_types=1);

namespace Modules\Consultation\Domain\Interfaces\Repositories;

use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Modules\Consultation\Domain\Dto\CreateNewPartTimeConsultationDto;
use Illuminate\Database\Eloquent\Collection;
use Modules\Consultation\Domain\Enums\ConsultationType;

interface ConsultationRepositoryInterface
{
    /**
     * Creates
     */
    public function createNewSemesterConsultation(CreateNewSemesterConsultationDto $dto): int;

    public function createNewSessionConsultation(CreateNewSessionConsultationDto $dto): int;

    public function createNewPartTimeConsultation(CreateNewPartTimeConsultationDto $dto): int;

    /**
     * Gets
     */
    public function getSemesterConsultations(int $scientificWorkerId, int $semesterId): array;

    public function getSessionConsultations(int $scientificWorkerId, int $semesterId): array;

    public function getPartTimeConsultations(int $scientificWorkerId, int $semesterId): array;

    /**
     * Deletes
     */
    public function deleteSemesterConsultation(int $consultationId, int $scientificWorkerId): bool;

    public function deleteSessionConsultation(int $consultationId, int $scientificWorkerId): bool;

    public function deletePartTimeConsultation(int $consultationId, int $scientificWorkerId): bool;

    public function getLastUpdateDateForSemesterConsultation(int $scientificWorkerId, int $semesterId): ?string;

    public function getLastUpdateDateForSessionConsultation(int $scientificWorkerId, int $semesterId): ?string;

    public function getLastUpdateDateForPartTimeConsultation(int $scientificWorkerId, int $semesterId): ?string;

    public function getConsultationSummaryTime(int $scientificWorkerId): ?string;

    public function getAllScientificWorkersWithConsultations(int $semesterId, ConsultationType $type): Collection;

    public function getScientificWorkersWithoutConsultations(int $semesterId, ConsultationType $type): Collection;
}
