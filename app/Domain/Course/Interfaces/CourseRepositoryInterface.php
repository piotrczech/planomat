<?php

declare(strict_types=1);

namespace App\Domain\Course\Interfaces;

use App\Domain\Dto\StoreCourseDto;
use App\Domain\Dto\UpdateCourseDto;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface
{
    public function findById(int $id): ?Course;

    public function getAll(?string $searchTerm = null): LengthAwarePaginator;

    public function create(StoreCourseDto $data): Course;

    public function update(int $id, UpdateCourseDto $data): ?Course;

    public function delete(int $id): bool;

    public function getCourseByName(string $name): ?Course;

    public function getAllCourses(): Collection;
}
