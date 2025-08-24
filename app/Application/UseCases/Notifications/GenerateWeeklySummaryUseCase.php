<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Application\UseCases\Semester\GetActiveConsultationSemesterUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Domain\Dto\WeeklySummaryDto;
use App\Domain\Enums\ActivityLogModuleEnum;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Infrastructure\Models\ActivityLog;
use Carbon\Carbon;

final readonly class GenerateWeeklySummaryUseCase
{
    public function __construct(
        private SettingRepositoryInterface $settingRepository,
        private GetActiveConsultationSemesterUseCase $getActiveConsultationSemesterUseCase,
        private GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
    ) {
    }

    public function execute(): ?WeeklySummaryDto
    {
        $settings = $this->settingRepository->getSettings([
            'notifications_weekly_semester_summary_enabled',
        ]);

        if (!$settings->get('notifications_weekly_semester_summary_enabled')) {
            return null;
        }

        $lastWeekStart = Carbon::now()->startOfWeek()->subWeek();
        $lastWeekEnd = Carbon::now()->endOfWeek()->subWeek();

        $lastWeekActivities = ActivityLog::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->with('user')
            ->get();

        $consultationActivities = $lastWeekActivities->where('module', ActivityLogModuleEnum::CONSULTATION);
        $desiderataActivities = $lastWeekActivities->where('module', ActivityLogModuleEnum::DESIDERATA);

        $generalActivity = [
            'consultation_changes' => $consultationActivities->count(),
            'desiderata_changes' => $desiderataActivities->count(),
        ];

        $consultationsActivity = $consultationActivities
            ->unique('user_id')
            ->map(fn ($activity) => $activity->user)
            ->map(fn ($user) => $user->fullName() . ' (' . $user->email . ')');

        $desiderataActivity = $desiderataActivities
            ->unique('user_id')
            ->map(fn ($activity) => $activity->user)
            ->map(fn ($user) => $user->fullName() . ' (' . $user->email . ')');

        $isConsultationSemesterActive = $this->getActiveConsultationSemesterUseCase->execute() !== null;
        $isDesiderataSemesterActive = $this->getActiveDesiderataSemesterUseCase->execute() !== null;

        return new WeeklySummaryDto(
            generalActivity: $generalActivity,
            consultationsActivity: $consultationsActivity,
            desiderataActivity: $desiderataActivity,
            weekStart: $lastWeekStart->format('Y-m-d'),
            weekEnd: $lastWeekEnd->format('Y-m-d'),
            isConsultationSemesterActive: $isConsultationSemesterActive,
            isDesiderataSemesterActive: $isDesiderataSemesterActive,
        );
    }
}
