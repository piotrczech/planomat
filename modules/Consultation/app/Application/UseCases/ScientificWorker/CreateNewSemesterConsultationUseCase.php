<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
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
    ) {
    }

    public function execute(CreateNewSemesterConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemesterId = 1;
        // $currentSemester = Semester::getCurrentSemester();

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
                $currentSemesterId,
                $dto,
            );
        } else {
            $result = $this->consultationRepository->createWeekendConsultations(
                $scientificWorkerId,
                $currentSemesterId,
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
