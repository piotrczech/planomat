<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Domain\ActivityLog\Dto\StoreActivityLogDto;
use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSessionConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewSessionConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly StoreActivityLogUseCase $storeActivityLogUseCase,
    ) {
    }

    public function execute(CreateNewSessionConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();

        $resultId = $this->consultationRepository->createSessionConsultation(
            $scientificWorkerId,
            $dto,
        );

        $this->storeActivityLogUseCase->execute(
            new StoreActivityLogDto(
                userId: (string) $scientificWorkerId,
                module: ActivityLogModuleEnum::CONSULTATION->value,
                action: ActivityLogActionEnum::CREATE->value,
            ),
        );

        return $resultId;
    }
}
