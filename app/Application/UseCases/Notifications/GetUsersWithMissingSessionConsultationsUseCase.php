<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Enums\RoleEnum;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\Semester;
use Illuminate\Support\Collection;
use Modules\Consultation\Infrastructure\Models\SessionConsultation;
use Carbon\Carbon;

final readonly class GetUsersWithMissingSessionConsultationsUseCase
{
    public function __construct(
        private SettingRepositoryInterface $settingRepository,
    ) {
    }

    public function execute(): Collection
    {
        $settings = $this->settingRepository->getSettings([
            'active_semester_for_consultations_id',
            'notifications_session_consultations_enabled',
            'notifications_session_consultations_days_after',
        ]);

        if (!$settings->get('notifications_session_consultations_enabled')) {
            return collect();
        }

        $semesterId = $settings->get('active_semester_for_consultations_id');

        if (!$semesterId) {
            return collect();
        }

        $semester = Semester::find($semesterId);

        if (!$semester || !$semester->session_start_date) {
            return collect();
        }

        $daysAfter = (int) $settings->get('notifications_session_consultations_days_after', 0);
        $notificationDate = Carbon::parse($semester->session_start_date)->addDays($daysAfter);

        // Sprawdź czy już czas na powiadomienia
        if (Carbon::now()->lt($notificationDate)) {
            return collect();
        }

        // Pobierz wszystkich pracowników naukowych
        $scientificWorkers = User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereNull('deleted_at')
            ->get();

        // Sprawdź którzy nie mają konsultacji w sesji
        $usersWithMissingConsultations = collect();

        foreach ($scientificWorkers as $user) {
            $hasConsultations = SessionConsultation::where('scientific_worker_id', $user->id)
                ->where('semester_id', $semesterId)
                ->exists();

            if (!$hasConsultations) {
                $usersWithMissingConsultations->push([
                    'user' => $user,
                    'semester' => $semester,
                    'days_overdue' => Carbon::now()->diffInDays($notificationDate),
                ]);
            }
        }

        return $usersWithMissingConsultations;
    }
}
