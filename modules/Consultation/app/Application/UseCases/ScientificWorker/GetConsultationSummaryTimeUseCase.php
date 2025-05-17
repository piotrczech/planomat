<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Illuminate\Support\Facades\Auth;

final class GetConsultationSummaryTimeUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(): ?string
    {
        $scientificWorkerId = Auth::id();

        if (!$scientificWorkerId) {
            return null;
        }

        return $this->consultationRepository->getConsultationSummaryTime(
            $scientificWorkerId,
        );
    }
}
