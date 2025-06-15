<?php

declare(strict_types=1);

namespace App\Application\UseCases\Course;

use App\Domain\Interfaces\CourseRepositoryInterface;
use App\Infrastructure\Models\Course;

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
