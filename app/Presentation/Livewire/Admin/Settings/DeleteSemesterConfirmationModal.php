<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Semester\GetSemesterUseCase;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DeleteSemesterConfirmationModal extends Component
{
    public int $semesterId;

    public string $semesterDetails = '';

    public function mount(Application $app, int $semesterId): void
    {
        $this->semesterId = $semesterId;
        $getSemesterUseCase = $app->make(GetSemesterUseCase::class);
        $semester = $getSemesterUseCase->execute($this->semesterId);

        if ($semester) {
            $seasonName = $semester->season->label();
            $this->semesterDetails = sprintf(
                '%s %s/%s',
                $seasonName,
                $semester->start_year,
                $semester->start_year + 1,
            );
        } else {
            $this->semesterDetails = __('admin_settings.semester_manager.unknown_semester');
        }
    }

    public function confirmDelete(): void
    {
        $this->dispatch('deleteSemesterConfirmed', $this->semesterId);
    }

    public function closeModal(): void
    {
        $this->dispatch('closeDeleteConfirmationModal');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.delete-semester-confirmation-modal');
    }
}
