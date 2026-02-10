<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final readonly class GetOtherScientificWorkerSessionConsultationsUseCase
{
    public function __construct(
        private ConsultationRepositoryInterface $consultationRepository,
        private GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
    ) {
    }

    public function execute(int $scientificWorkerId): array
    {
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            return [];
        }

        return $this->consultationRepository->getSessionConsultations(
            $scientificWorkerId,
            $currentSemester->id,
        );
    }
}
