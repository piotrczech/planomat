<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Dto\StoreCourseDto;
use App\Domain\Dto\UpdateCourseDto;
use App\Domain\Course\Interfaces\CourseRepositoryInterface;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class CourseRepository implements CourseRepositoryInterface
{
    private const DEFAULT_PER_PAGE = 15;

    public function findById(int $id): ?Course
    {
        return Course::find($id);
    }

    public function getAll(?string $searchTerm = null): LengthAwarePaginator
    {
        return Course::query()
            ->when($searchTerm, function ($query, $searchTerm): void {
                $query->where('name', 'like', "%{$searchTerm}%");
            })
            ->orderBy('name')
            ->paginate(self::DEFAULT_PER_PAGE);
    }

    public function create(StoreCourseDto $data): Course
    {
        return Course::create([
            'name' => $data->name,
        ]);
    }

    public function update(int $id, UpdateCourseDto $data): ?Course
    {
        $course = $this->findById($id);

        if ($course) {
            $course->update([
                'name' => $data->name,
            ]);

            return $course->fresh();
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $course = $this->findById($id);

        if ($course) {
            return $course->delete();
        }

        return false;
    }

    public function getCourseByName(string $name): ?Course
    {
        return Course::where('name', $name)->first();
    }

    public function getAllCourses(): Collection
    {
        return Course::orderBy('name')->get();
    }
}
