<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Course\DeleteCourseUseCase;
use App\Application\UseCases\Course\ListCoursesUseCase;
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
    }

    public function handleDeleteCourseConfirmed(DeleteCourseUseCase $deleteCourseUseCase): void
    {
        if ($this->deletingCourseId) {
            $deleteCourseUseCase->execute($this->deletingCourseId);
            $this->deletingCourseId = null;
            $this->showDeleteConfirmationModal = false;
        }
    }

    public function openCreateCourseModal(): void
    {
        $this->editingCourseId = null;
        $this->resetErrorBag();
        $this->showCourseFormModal = true;
    }

    public function openEditCourseModal(int $courseId): void
    {
        $this->editingCourseId = $courseId;
        $this->resetErrorBag();
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
