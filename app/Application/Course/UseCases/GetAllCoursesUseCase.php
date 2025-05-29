<?php

declare(strict_types=1);

namespace App\Application\Course\UseCases;

use App\Domain\Course\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetAllCoursesUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(): Collection
    {
        return $this->courseRepository->getAllCourses();
    }
}
