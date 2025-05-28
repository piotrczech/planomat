<?php

declare(strict_types=1);

namespace App\Application\Course\UseCases;

use App\Domain\Dto\StoreCourseDto;
use App\Domain\Course\Interfaces\CourseRepositoryInterface;
use App\Models\Course;

final readonly class CreateCourseUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(StoreCourseDto $data): Course
    {
        // Business logic specific to creating a course can be added here if needed.
        // For example, dispatching an event after creation.
        // Validation is handled by the StoreCourseDto DTO.
        return $this->courseRepository->create($data);
    }
}
