<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Semester\GetAllSemestersUseCase;
use App\Application\UseCases\Setting\GetSettingsUseCase;
use App\Application\UseCases\Setting\UpdateSettingsUseCase;
use Illuminate\Support\Collection;
use Livewire\Component;

class GlobalSettings extends Component
{
    public ?string $activeSemesterForConsultationsId = null;

    public ?string $activeSemesterForDesiderataId = null;

    public Collection $semesters;

    public bool $notificationsSemesterConsultationsEnabled = true;

    public int $notificationsSemesterConsultationsDaysAfter = 21;

    public bool $notificationsDesiderataEnabled = true;

    public int $notificationsDesiderataDaysAfter = 21;

    public bool $notificationsSessionConsultationsEnabled = true;

    public int $notificationsSessionConsultationsDaysAfter = 0;

    public bool $notificationsWeeklySemesterSummaryEnabled = true;

    public function mount(
        GetAllSemestersUseCase $getAllSemestersUseCase,
        GetSettingsUseCase $getSettingsUseCase,
    ): void {
        $this->semesters = $getAllSemestersUseCase->execute();

        $settings = $getSettingsUseCase->execute([
            'active_semester_for_consultations_id',
            'active_semester_for_desiderata_id',
            'notifications_semester_consultations_enabled',
            'notifications_semester_consultations_days_after',
            'notifications_desiderata_enabled',
            'notifications_desiderata_days_after',
            'notifications_session_consultations_enabled',
            'notifications_session_consultations_days_after',
            'notifications_weekly_semester_summary_enabled',
        ]);

        $this->activeSemesterForConsultationsId = $settings->get('active_semester_for_consultations_id');
        $this->activeSemesterForDesiderataId = $settings->get('active_semester_for_desiderata_id');

        $this->notificationsSemesterConsultationsEnabled = (bool) $settings->get('notifications_semester_consultations_enabled', true);
        $this->notificationsSemesterConsultationsDaysAfter = (int) $settings->get('notifications_semester_consultations_days_after', 21);
        $this->notificationsDesiderataEnabled = (bool) $settings->get('notifications_desiderata_enabled', true);
        $this->notificationsDesiderataDaysAfter = (int) $settings->get('notifications_desiderata_days_after', 21);
        $this->notificationsSessionConsultationsEnabled = (bool) $settings->get('notifications_session_consultations_enabled', true);
        $this->notificationsSessionConsultationsDaysAfter = (int) $settings->get('notifications_session_consultations_days_after', 0);
        $this->notificationsWeeklySemesterSummaryEnabled = (bool) $settings->get('notifications_weekly_semester_summary_enabled', true);
    }

    public function save(UpdateSettingsUseCase $updateSettingsUseCase): void
    {
        $validated = $this->validate([
            'activeSemesterForConsultationsId' => ['nullable', 'exists:semesters,id'],
            'activeSemesterForDesiderataId' => ['nullable', 'exists:semesters,id'],
            'notificationsSemesterConsultationsEnabled' => ['boolean'],
            'notificationsSemesterConsultationsDaysAfter' => ['integer', 'min:1', 'max:30'],
            'notificationsDesiderataEnabled' => ['boolean'],
            'notificationsDesiderataDaysAfter' => ['integer', 'min:1', 'max:30'],
            'notificationsSessionConsultationsEnabled' => ['boolean'],
            'notificationsSessionConsultationsDaysAfter' => ['integer', 'min:1', 'max:7'],
            'notificationsWeeklySemesterSummaryEnabled' => ['boolean'],
        ]);

        $updateSettingsUseCase->execute([
            'active_semester_for_consultations_id' => $validated['activeSemesterForConsultationsId'],
            'active_semester_for_desiderata_id' => $validated['activeSemesterForDesiderataId'],
            'notifications_semester_consultations_enabled' => $validated['notificationsSemesterConsultationsEnabled'] ? '1' : '0',
            'notifications_semester_consultations_days_after' => (string) $validated['notificationsSemesterConsultationsDaysAfter'],
            'notifications_desiderata_enabled' => $validated['notificationsDesiderataEnabled'] ? '1' : '0',
            'notifications_desiderata_days_after' => (string) $validated['notificationsDesiderataDaysAfter'],
            'notifications_session_consultations_enabled' => $validated['notificationsSessionConsultationsEnabled'] ? '1' : '0',
            'notifications_session_consultations_days_after' => (string) $validated['notificationsSessionConsultationsDaysAfter'],
            'notifications_weekly_semester_summary_enabled' => $validated['notificationsWeeklySemesterSummaryEnabled'] ? '1' : '0',
        ]);

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.admin.settings.global-settings');
    }
}
