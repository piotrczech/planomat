<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewPartTimeConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewPartTimeConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
        private readonly CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(CreateNewPartTimeConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getActiveConsultationSemesterUseCase->execute();

        if (!$currentSemester) {
            return 0;
        }

        $resultId = $this->consultationRepository->createNewPartTimeConsultation($dto);

        if ($resultId > 0) {
            $this->createActivityLogUseCase->execute(
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
