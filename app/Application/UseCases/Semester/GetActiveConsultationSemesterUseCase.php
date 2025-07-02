<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Infrastructure\Models\Semester;

final readonly class GetActiveConsultationSemesterUseCase
{
    public function __construct(
        private SettingRepositoryInterface $settingRepository,
        private SemesterRepositoryInterface $semesterRepository,
    ) {
    }

    public function execute(): ?Semester
    {
        $settings = $this->settingRepository->getSettings(['active_semester_for_consultations_id']);
        $semesterId = $settings->get('active_semester_for_consultations_id');

        if (!$semesterId) {
            return null;
        }

        return $this->semesterRepository->findById((int) $semesterId);
    }
}
