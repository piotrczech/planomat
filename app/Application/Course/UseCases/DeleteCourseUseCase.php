<?php

declare(strict_types=1);

namespace App\Application\Course\UseCases;

use App\Domain\Course\Interfaces\CourseRepositoryInterface;

final readonly class DeleteCourseUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(int $id): bool
    {
        return $this->courseRepository->delete($id);
    }
}
