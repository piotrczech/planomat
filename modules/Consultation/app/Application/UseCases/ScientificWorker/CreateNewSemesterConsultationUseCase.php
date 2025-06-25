<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewSemesterConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(CreateNewSemesterConsultationDto $dto): int|bool
    {
        $result = $this->consultationRepository->createNewSemesterConsultation($dto);

        if ($result) {
            $this->createActivityLogUseCase->execute(
                new StoreActivityLogDto(
                    userId: (string) Auth::id(),
                    module: ActivityLogModuleEnum::CONSULTATION->value,
                    action: ActivityLogActionEnum::CREATE->value,
                ),
            );
        }

        return $result;
    }
}
