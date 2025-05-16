<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Enums\WeekdayEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Dto\CreateNewSemesterConsultationDto;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class CreateNewSemesterConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(CreateNewSemesterConsultationDto $dto): int
    {
        $scientificWorkerId = Auth::id();
        $currentSemesterId = 1;
        // $currentSemester = Semester::getCurrentSemester();

        if (in_array($dto->consultationWeekday, [
            WeekdayEnum::MONDAY->value,
            WeekdayEnum::TUESDAY->value,
            WeekdayEnum::WEDNESDAY->value,
            WeekdayEnum::THURSDAY->value,
            WeekdayEnum::FRIDAY->value,
        ])) {
            $result = $this->consultationRepository->createWeekdayConsultation(
                $scientificWorkerId,
                $currentSemesterId,
                $dto,
            );

            return $result ? 1 : 0;
        }

        return $this->consultationRepository->createWeekendConsultations(
            $scientificWorkerId,
            $currentSemesterId,
            $dto,
        );
    }
}
