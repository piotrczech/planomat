<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Dashboard;

use App\Application\UseCases\Semester\GetAllSemestersUseCase;
use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class AdminDashboardActions extends Component
{
    public Collection $semesters;

    public ?int $selectedSemesterId = null;

    public function mount(GetAllSemestersUseCase $getAllSemestersUseCase): void
    {
        $this->semesters = $getAllSemestersUseCase->execute();
        $this->selectedSemesterId = $this->semesters->first()?->id;
    }

    #[Computed]
    public function semestersForTomSelect(): string
    {
        return $this->semesters->map(function ($semester) {
            return [
                'id' => $semester->id,
                'name' => sprintf(
                    '%d/%d (%s)',
                    $semester->start_year,
                    $semester->start_year + 1,
                    $semester->season->label(),
                ),
                'dates' => $semester->semester_start_date->format('d.m.Y') . ' - ' . $semester->end_date->format('d.m.Y'),
            ];
        })->toJson();
    }

    public function openDesiderataExportModal(): void
    {
        if (!$this->selectedSemesterId) {
            return;
        }

        $this->dispatch('openDesiderataExportModal', semesterId: $this->selectedSemesterId)->to(ExportDesiderataModal::class);
    }

    public function openConsultationExportModal(): void
    {
        if (!$this->selectedSemesterId) {
            return;
        }

        $this->dispatch('openConsultationExportModal', semesterId: $this->selectedSemesterId)->to(ExportConsultationsModal::class);
    }

    public function render()
    {
        return view('livewire.admin.dashboard.admin-dashboard-actions');
    }
}
