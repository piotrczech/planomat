<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\Course\CreateCourseUseCase;
use App\Application\UseCases\Course\GetCourseUseCase;
use App\Application\UseCases\Course\UpdateCourseUseCase;
use App\Domain\Dto\StoreCourseDto;
use App\Domain\Dto\UpdateCourseDto;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class CourseFormModal extends Component
{
    public ?int $courseId = null;

    public string $name = '';

    public function mount(Application $app, ?int $courseId = null): void
    {
        $this->courseId = $courseId;

        if ($this->courseId) {
            $getCourseUseCase = $app->make(GetCourseUseCase::class);
            $course = $getCourseUseCase->execute($this->courseId);

            if ($course) {
                $this->name = $course->name;
            }
        }
    }

    public function saveCourse(Application $app): void
    {
        $dataToValidate = ['name' => $this->name];

        if ($this->courseId) {
            $dataToValidate['id'] = $this->courseId;
        }

        if ($this->courseId) {
            $courseData = UpdateCourseDto::from($dataToValidate);
            $updateCourseUseCase = $app->make(UpdateCourseUseCase::class);
            $updateCourseUseCase->execute($this->courseId, $courseData);
        } else {
            $courseData = StoreCourseDto::from($dataToValidate);
            $createCourseUseCase = $app->make(CreateCourseUseCase::class);
            $createCourseUseCase->execute($courseData);
        }

        $this->dispatch('courseSaved');
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->dispatch('closeCourseFormModal');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.course-form-modal');
    }
}
