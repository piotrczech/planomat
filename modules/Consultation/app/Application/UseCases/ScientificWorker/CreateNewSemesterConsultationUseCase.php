<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Application\Semester\UseCases\GetCurrentSemesterUseCase;
use App\Domain\ActivityLog\Dto\StoreActivityLogDto;
use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use App\Enums\WeekdayEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewSemesterConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly StoreActivityLogUseCase $storeActivityLogUseCase,
        private readonly GetCurrentSemesterUseCase $getCurrentSemesterUseCase,
    ) {
    }

    public function execute(CreateNewSemesterConsultationDto $dto): bool
    {
        $scientificWorkerId = Auth::id();
        $currentSemester = $this->getCurrentSemesterUseCase->execute();

        if (!$currentSemester) {
            return false;
        }

        // todo: validation that consultation not overlaps with other consultations
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
            $this->storeActivityLogUseCase->execute(
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
