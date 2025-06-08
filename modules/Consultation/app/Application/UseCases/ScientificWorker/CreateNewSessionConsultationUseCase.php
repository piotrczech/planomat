<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Application\Semester\UseCases\GetCurrentSemesterUseCase;
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
        private readonly GetCurrentSemesterUseCase $getCurrentSemesterUseCase,
        private readonly StoreActivityLogUseCase $storeActivityLogUseCase,
    ) {
    }

    public function execute(CreateNewSessionConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getCurrentSemesterUseCase->execute();

        if (!$currentSemester) {
            return 0;
        }

        $resultId = $this->consultationRepository->createSessionConsultation(
            $scientificWorkerId,
            $currentSemester->id,
            $dto,
        );

        if ($resultId > 0) {
            $this->storeActivityLogUseCase->execute(
                new StoreActivityLogDto(
                    userId: (string) $scientificWorkerId,
                    module: ActivityLogModuleEnum::CONSULTATION->value,
                    action: ActivityLogActionEnum::CREATE->value,
                ),
            );
        }

        return $resultId;
    }
}
