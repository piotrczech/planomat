<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Dto\ScientificWorkerActionStatusDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Carbon\Carbon;

final class GetScientificWorkerActionsUseCase
{
    public function __construct(
        private readonly SemesterRepositoryInterface $semesterRepository,
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function execute(): ScientificWorkerActionStatusDto
    {
        $user = Auth::user();
        $currentSemester = $this->semesterRepository->findCurrentSemester();

        if (!$user || !$currentSemester) {
            return new ScientificWorkerActionStatusDto;
        }

        $now = Carbon::now();

        // Desiderata Logic
        $hasDesiderata = $this->desideratumRepository->getDesideratumForUserAndSemester($user->id, $currentSemester->id) !== null;
        $desiderataDueDate = $currentSemester->end_date;
        $showDesiderata = !$hasDesiderata && $now->isBefore($desiderataDueDate);
        $desiderataDueDays = $now->diffInDays($desiderataDueDate, false);

        // Semester Consultations Logic
        $hasSemesterConsultations = !empty($this->consultationRepository->getSemesterConsultations($user->id, $currentSemester->id));
        $semesterConsultationsDueDate = $currentSemester->session_start_date;
        $showSemesterConsultations = !$hasSemesterConsultations && $now->isBefore($semesterConsultationsDueDate);
        $semesterActiveForDays = $currentSemester->semester_start_date->diffInDays($now);

        // Session Consultations Logic
        $hasSessionConsultations = !empty($this->consultationRepository->getSessionConsultations($user->id, $currentSemester->id));
        $sessionConsultationsStartDate = $currentSemester->session_start_date->copy()->subWeeks(2);
        $sessionConsultationsEndDate = $currentSemester->end_date;
        $showSessionConsultations = !$hasSessionConsultations && $now->between($sessionConsultationsStartDate, $sessionConsultationsEndDate);
        $sessionConsultationsDueDays = $now->diffInDays($sessionConsultationsEndDate, false);

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
