<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewSessionConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(CreateNewSessionConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();

        $resultId = $this->consultationRepository->createSessionConsultation(
            $scientificWorkerId,
            $dto,
        );

        return $resultId;
    }
}
