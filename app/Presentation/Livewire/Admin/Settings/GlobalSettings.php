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

    public function mount(
        GetAllSemestersUseCase $getAllSemestersUseCase,
        GetSettingsUseCase $getSettingsUseCase,
    ): void {
        $this->semesters = $getAllSemestersUseCase->execute();

        $settings = $getSettingsUseCase->execute([
            'active_semester_for_consultations_id',
            'active_semester_for_desiderata_id',
        ]);

        $this->activeSemesterForConsultationsId = $settings->get('active_semester_for_consultations_id');
        $this->activeSemesterForDesiderataId = $settings->get('active_semester_for_desiderata_id');
    }

    public function save(UpdateSettingsUseCase $updateSettingsUseCase): void
    {
        $validated = $this->validate([
            'activeSemesterForConsultationsId' => ['nullable', 'exists:semesters,id'],
            'activeSemesterForDesiderataId' => ['nullable', 'exists:semesters,id'],
        ]);

        $updateSettingsUseCase->execute([
            'active_semester_for_consultations_id' => $validated['activeSemesterForConsultationsId'],
            'active_semester_for_desiderata_id' => $validated['activeSemesterForDesiderataId'],
        ]);

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.admin.settings.global-settings');
    }
}
