<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class GetSessionConsultationsUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(): array
    {
        $scientificWorkerId = Auth::id();

        return $this->consultationRepository->getSessionConsultations(
            $scientificWorkerId,
        );
    }
}
