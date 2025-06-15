<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Course\GetCourseUseCase;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DeleteCourseConfirmationModal extends Component
{
    public int $courseId;

    public string $courseName = '';

    public function mount(Application $app, int $courseId): void
    {
        $this->courseId = $courseId;
        $getCourseUseCase = $app->make(GetCourseUseCase::class);
        $course = $getCourseUseCase->execute($this->courseId);

        if ($course) {
            $this->courseName = $course->name;
        } else {
            $this->courseName = __('admin_settings.course_manager.unknown_course');
        }
    }

    public function confirmDelete(): void
    {
        $this->dispatch('deleteCourseConfirmed', $this->courseId);
    }

    public function closeModal(): void
    {
        $this->dispatch('closeDeleteConfirmationModal');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.delete-course-confirmation-modal');
    }
}
