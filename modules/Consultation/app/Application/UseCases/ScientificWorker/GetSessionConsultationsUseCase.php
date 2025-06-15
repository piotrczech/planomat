<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use App\Domain\Interfaces\SemesterRepositoryInterface;

final class GetSessionConsultationsUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    public function execute(): array
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->semesterRepository->findCurrentSemester();

        if (!$currentSemester) {
            return [];
        }

        return $this->consultationRepository->getSessionConsultations($scientificWorkerId, $currentSemester->id);
    }
}
