<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Illuminate\Support\Facades\Auth;

final class GetLastUpdateDateForPartTimeConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
    ) {
    }

    public function execute(): ?string
    {
        $scientificWorkerId = Auth::id();

        if (!$scientificWorkerId) {
            return null;
        }

        $activeSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$activeSemester) {
            return null;
        }

        return $this->consultationRepository->getLastUpdateDateForPartTimeConsultation($scientificWorkerId, $activeSemester->id);
    }
}
