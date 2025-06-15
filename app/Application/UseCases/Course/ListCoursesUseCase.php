<?php

declare(strict_types=1);

namespace App\Application\UseCases\Course;

use App\Domain\Interfaces\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListCoursesUseCase
{
    public function __construct(private CourseRepositoryInterface $courseRepository)
    {
    }

    public function execute(?string $searchTerm = null): LengthAwarePaginator
    {
        return $this->courseRepository->getAll($searchTerm);
    }
}
