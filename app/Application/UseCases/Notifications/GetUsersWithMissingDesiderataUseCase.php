<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Enums\RoleEnum;
use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\Semester;
use Illuminate\Support\Collection;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use Carbon\Carbon;

final readonly class GetUsersWithMissingDesiderataUseCase
{
    public function __construct(
        private SettingRepositoryInterface $settingRepository,
    ) {
    }

    public function execute(): Collection
    {
        $settings = $this->settingRepository->getSettings([
            'active_semester_for_desiderata_id',
            'notifications_desiderata_enabled',
            'notifications_desiderata_days_after',
        ]);

        if (!$settings->get('notifications_desiderata_enabled')) {
            return collect();
        }

        $semesterId = $settings->get('active_semester_for_desiderata_id');

        if (!$semesterId) {
            return collect();
        }

        $semester = Semester::find($semesterId);

        if (!$semester) {
            return collect();
        }

        $daysAfter = (int) $settings->get('notifications_desiderata_days_after', 21);
        $notificationDate = Carbon::parse($semester->semester_start_date)->addDays($daysAfter);

        // Sprawdź czy już czas na powiadomienia
        if (Carbon::now()->lt($notificationDate)) {
            return collect();
        }

        // Pobierz wszystkich pracowników naukowych
        $scientificWorkers = User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereNull('deleted_at')
            ->get();

        // Sprawdź którzy nie mają dezyderatów
        $usersWithMissingDesiderata = collect();

        foreach ($scientificWorkers as $user) {
            $hasDesiderata = Desideratum::where('scientific_worker_id', $user->id)
                ->where('semester_id', $semesterId)
                ->exists();

            if (!$hasDesiderata) {
                $usersWithMissingDesiderata->push([
                    'user' => $user,
                    'semester' => $semester,
                    'days_overdue' => Carbon::now()->diffInDays($notificationDate),
                ]);
            }
        }

        return $usersWithMissingDesiderata;
    }
}
