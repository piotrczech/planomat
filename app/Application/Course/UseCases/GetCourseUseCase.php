<?php

declare(strict_types=1);

namespace App\Application\Course\UseCases;

use App\Domain\Course\Interfaces\CourseRepositoryInterface;
use App\Models\Course;

final readonly class GetCourseUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(int $id): ?Course
    {
        return $this->courseRepository->findById($id);
    }
}
