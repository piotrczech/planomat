<?php

declare(strict_types=1);

namespace App\Application\UseCases\Course;

use App\Domain\Dto\UpdateCourseDto;
use App\Domain\Interfaces\CourseRepositoryInterface;
use App\Infrastructure\Models\Course;

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
