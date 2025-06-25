<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use Illuminate\Support\Facades\DB;

final readonly class DeleteSemesterUseCase
{
    public function __construct(
        private SemesterRepositoryInterface $semesterRepository,
        private CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(int $id): bool
    {
        $deleted = DB::transaction(function () use ($id): bool {
            $semester = $this->semesterRepository->findById($id);

            if (!$semester) {
                return false;
            }

            $semester->load(['desiderata.coursePreferences', 'desiderata.unavailableTimeSlots']);

            foreach ($semester->desiderata as $desideratum) {
                $desideratum->coursePreferences()->delete();
                $desideratum->unavailableTimeSlots()->delete();
            }

            $semester->desiderata()->delete();
            $semester->semesterConsultations()->delete();
            $semester->sessionConsultations()->delete();
            $semester->partTimeConsultations()->delete();

            return $this->semesterRepository->delete($id);
        });

        return $deleted;
    }
}
