<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class GetSemesterConsultationsUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(int $semesterId): array
    {
        $scientificWorkerId = Auth::id();

        return $this->consultationRepository->getSemesterConsultations(
            $scientificWorkerId,
            $semesterId,
        );
    }
}
