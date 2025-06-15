<?php

declare(strict_types=1);

namespace App\Application\UseCases\Course;

use App\Domain\Dto\StoreCourseDto;
use App\Domain\Interfaces\CourseRepositoryInterface;
use App\Infrastructure\Models\Course;

final readonly class CreateCourseUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(StoreCourseDto $data): Course
    {
        return $this->courseRepository->create($data);
    }
}
