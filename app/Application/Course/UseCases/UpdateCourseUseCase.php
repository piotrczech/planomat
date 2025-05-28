<?php

declare(strict_types=1);

namespace App\Application\Course\UseCases;

use App\Domain\Dto\UpdateCourseDto;
use App\Domain\Course\Interfaces\CourseRepositoryInterface;
use App\Models\Course;

final readonly class UpdateCourseUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(int $id, UpdateCourseDto $data): ?Course
    {
        return $this->courseRepository->update($id, $data);
    }
}
