<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class GetPartTimeConsultationsUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
    ) {
    }

    public function execute(): array
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            return [];
        }

        return $this->consultationRepository->getPartTimeConsultations($scientificWorkerId, $currentSemester->id);
    }
}
