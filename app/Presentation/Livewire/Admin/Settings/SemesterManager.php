<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Semester\DeleteSemesterUseCase;
use App\Application\UseCases\Semester\ListSemestersUseCase;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class SemesterManager extends Component
{
    use WithPagination;

    public bool $showSemesterFormModal = false;

    public bool $showDeleteConfirmationModal = false;

    public ?int $editingSemesterId = null;

    public ?int $deletingSemesterId = null;

    public string $semesterSearch = '';

    protected $listeners = [
        'semesterSaved' => 'handleSemesterSaved',
        'deleteSemesterConfirmed' => 'handleDeleteSemesterConfirmed',
        'closeSemesterFormModal' => 'closeSemesterFormModal',
        'closeDeleteConfirmationModal' => 'closeDeleteConfirmationModal',
    ];

    public function updatedSemesterSearch(): void
    {
        $this->resetPage();
    }

    public function handleSemesterSaved(): void
    {
        $this->showSemesterFormModal = false;
        $this->editingSemesterId = null;
        // Tutaj można dodać powiadomienie dla użytkownika (np. toast)
        // $this->dispatch('notify', ['message' => __('admin_settings.semester_manager.notifications.semester_saved'), 'type' => 'success']);
        $this->resetPage(); // Odświeżenie listy
    }

    public function handleDeleteSemesterConfirmed(Application $app, int $semesterId): void
    {
        $deleteSemesterUseCase = $app->make(DeleteSemesterUseCase::class);

        if ($this->deletingSemesterId && $this->deletingSemesterId === $semesterId) {
            $deleteSemesterUseCase->execute($this->deletingSemesterId);
            $this->deletingSemesterId = null;
            $this->showDeleteConfirmationModal = false;
            // Tutaj można dodać powiadomienie dla użytkownika
            // $this->dispatch('notify', ['message' => __('admin_settings.semester_manager.notifications.semester_deleted'), 'type' => 'success']);
            $this->resetPage(); // Odświeżenie listy
        }
    }

    public function openCreateSemesterModal(): void
    {
        $this->editingSemesterId = null;
        $this->resetErrorBag();
        $this->showSemesterFormModal = true;
    }

    public function openEditSemesterModal(int $semesterId): void
    {
        $this->editingSemesterId = $semesterId;
        $this->resetErrorBag();
        $this->showSemesterFormModal = true;
    }

    public function closeSemesterFormModal(): void
    {
        $this->editingSemesterId = null;
        $this->showSemesterFormModal = false;
    }

    public function openDeleteConfirmationModal(int $semesterId): void
    {
        $this->deletingSemesterId = $semesterId;
        $this->showDeleteConfirmationModal = true;
    }

    public function closeDeleteConfirmationModal(): void
    {
        $this->deletingSemesterId = null;
        $this->showDeleteConfirmationModal = false;
    }

    public function render(Application $app): View
    {
        $listSemestersUseCase = $app->make(ListSemestersUseCase::class);
        $semesters = $listSemestersUseCase->execute($this->semesterSearch);

        return view('livewire.admin.settings.semester-manager', [
            'semesters' => $semesters,
        ]);
    }
}
