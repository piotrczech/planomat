<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Domain\Dto\ScientificWorkerActionStatusDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;

final class GetScientificWorkerActionsUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
        private readonly GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
    ) {
    }

    public function execute(): ScientificWorkerActionStatusDto
    {
        $user = Auth::user();

        if (!$user) {
            return new ScientificWorkerActionStatusDto;
        }

        $now = Carbon::now();
        $desiderataSemester = $this->getActiveDesiderataSemesterUseCase->execute();
        $consultationSemester = $this->getActiveConsultationSemesterUseCase->execute();

        $showDesiderata = false;
        $desiderataDueDays = 0;

        if ($desiderataSemester) {
            $hasDesiderata = $this->desideratumRepository->getDesideratumForUserAndSemester($user->id, $desiderataSemester->id) !== null;
            $desiderataDueDate = $desiderataSemester->end_date;
            $showDesiderata = !$hasDesiderata && $now->isBefore($desiderataDueDate);
            $desiderataDueDays = $now->diffInDays($desiderataDueDate, false);
        }

        $showSemesterConsultations = false;
        $semesterActiveForDays = 0;
        $showSessionConsultations = false;
        $sessionConsultationsDueDays = 0;

        if ($consultationSemester) {
            $hasSemesterConsultations = !empty($this->consultationRepository->getSemesterConsultations($user->id, $consultationSemester->id));
            $semesterConsultationsDueDate = $consultationSemester->session_start_date;
            $showSemesterConsultations = !$hasSemesterConsultations && $now->isBefore($semesterConsultationsDueDate);
            $semesterActiveForDays = $consultationSemester->semester_start_date->diffInDays($now);

            $hasSessionConsultations = !empty($this->consultationRepository->getSessionConsultations($user->id, $consultationSemester->id));
            $sessionConsultationsStartDate = $consultationSemester->session_start_date->copy()->subWeeks(2);
            $sessionConsultationsEndDate = $consultationSemester->end_date;
            $showSessionConsultations = !$hasSessionConsultations && $now->between($sessionConsultationsStartDate, $sessionConsultationsEndDate);
            $sessionConsultationsDueDays = $now->diffInDays($sessionConsultationsEndDate, false);
        }

        $anyActionsAvailable = $showDesiderata || $showSemesterConsultations || $showSessionConsultations;

        return new ScientificWorkerActionStatusDto(
            showDesiderata: $showDesiderata,
            desiderataDueDays: (int) ($desiderataDueDays > 0 ? $desiderataDueDays : 0),
            showSemesterConsultations: $showSemesterConsultations,
            semesterActiveForDays: (int) ($semesterActiveForDays > 0 ? $semesterActiveForDays : 0),
            showSessionConsultations: $showSessionConsultations,
            sessionConsultationsDueDays: (int) ($sessionConsultationsDueDays > 0 ? $sessionConsultationsDueDays : 0),
            anyActionsAvailable: $anyActionsAvailable,
        );
    }
}
