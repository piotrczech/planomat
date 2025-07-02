<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final readonly class GetSemesterConsultationsUseCase
{
    public function __construct(
        private ConsultationRepositoryInterface $consultationRepository,
        private GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
    ) {
    }

    public function execute(): array
    {
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            return [];
        }

        return $this->consultationRepository->getSemesterConsultations(
            Auth::id(),
            $currentSemester->id,
        );
    }
}
