<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Application\UseCases\Semester\GetCurrentSemesterUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use App\Domain\Enums\WeekdayEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewSemesterConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly CreateActivityLogUseCase $createActivityLogUseCase,
        private readonly GetCurrentSemesterUseCase $getCurrentSemesterUseCase,
    ) {
    }

    public function execute(CreateNewSemesterConsultationDto $dto): int|bool
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getCurrentSemesterUseCase->execute();

        if (!$currentSemester) {
            return 0;
        }

        $isWeekday = in_array($dto->consultationWeekday, [
            WeekdayEnum::MONDAY->value,
            WeekdayEnum::TUESDAY->value,
            WeekdayEnum::WEDNESDAY->value,
            WeekdayEnum::THURSDAY->value,
            WeekdayEnum::FRIDAY->value,
        ]);

        if ($isWeekday) {
            $result = $this->consultationRepository->createWeekdayConsultation(
                $scientificWorkerId,
                $currentSemester->id,
                $dto,
            );
        } else {
            $result = $this->consultationRepository->createWeekendConsultations(
                $scientificWorkerId,
                $currentSemester->id,
                $dto,
            );
        }

        if ($result) {
            $this->createActivityLogUseCase->execute(
                new StoreActivityLogDto(
                    userId: (string) $scientificWorkerId,
                    module: ActivityLogModuleEnum::CONSULTATION->value,
                    action: ActivityLogActionEnum::CREATE->value,
                ),
            );
        }

        return $result;
    }
}
