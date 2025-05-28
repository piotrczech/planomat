<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Application\Course\UseCases\DeleteCourseUseCase;
use App\Application\Course\UseCases\ListCoursesUseCase;
// use App\Domain\Course\DataTransferObjects\CourseData; // Usunięty import
// Założenie, że walidacja będzie w CourseFormModal
use App\Models\Course; // Można usunąć jeśli nie używamy bezpośrednio
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CourseManager extends Component
{
    use WithPagination;

    public bool $showCourseFormModal = false;

    public bool $showDeleteConfirmationModal = false;

    public ?int $editingCourseId = null;

    public ?int $deletingCourseId = null;

    public string $courseSearch = '';

    protected $listeners = [
        'courseSaved' => 'handleCourseSaved',
        'deleteCourseConfirmed' => 'handleDeleteCourseConfirmed',
        'closeCourseFormModal' => 'closeCourseFormModal',
        'closeDeleteConfirmationModal' => 'closeDeleteConfirmationModal',
    ];

    public function updatedCourseSearch(): void
    {
        $this->resetPage();
    }

    public function handleCourseSaved(): void
    {
        $this->showCourseFormModal = false;
        // Optionally, dispatch a browser event for user notification (e.g., a toast message)
        // $this->dispatch('notify', ['message' => __('admin_settings.course_manager.notifications.course_saved'), 'type' => 'success']);
    }

    public function handleDeleteCourseConfirmed(DeleteCourseUseCase $deleteCourseUseCase): void
    {
        if ($this->deletingCourseId) {
            $deleteCourseUseCase->execute($this->deletingCourseId);
            $this->deletingCourseId = null;
            $this->showDeleteConfirmationModal = false;
            // Optionally, dispatch a browser event for user notification
            // $this->dispatch('notify', ['message' => __('admin_settings.course_manager.notifications.course_deleted'), 'type' => 'success']);
        }
    }

    public function openCreateCourseModal(): void
    {
        $this->editingCourseId = null;
        $this->resetErrorBag(); // Clear previous validation errors
        $this->showCourseFormModal = true;
    }

    public function openEditCourseModal(int $courseId): void
    {
        $this->editingCourseId = $courseId;
        $this->resetErrorBag(); // Clear previous validation errors
        $this->showCourseFormModal = true;
    }

    public function closeCourseFormModal(): void
    {
        $this->editingCourseId = null;
        $this->showCourseFormModal = false;
    }

    public function openDeleteConfirmationModal(int $courseId): void
    {
        $this->deletingCourseId = $courseId;
        $this->showDeleteConfirmationModal = true;
    }

    public function closeDeleteConfirmationModal(): void
    {
        $this->deletingCourseId = null;
        $this->showDeleteConfirmationModal = false;
    }

    public function render(ListCoursesUseCase $listCoursesUseCase): View
    {
        $courses = $listCoursesUseCase->execute($this->courseSearch);

        return view('livewire.admin.settings.course-manager', [
            'courses' => $courses,
        ]);
    }
}
