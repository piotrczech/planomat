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

    public function getConsultationSummaryTime(int $scientificWorkerId): ?string;

    /**
     * Fetches all semester and session consultations, grouped by scientific worker, formatted for PDF export.
     *
     * @return array An array where keys are scientific worker IDs and values are arrays
     *               containing worker's name and their list of consultations.
     *               Each consultation should have 'type', 'term_or_day', 'hours', 'location', 'week_type' (if applicable).
     */
    public function fetchAllForPdfExport(): array;
}
