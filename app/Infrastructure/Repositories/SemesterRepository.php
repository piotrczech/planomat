<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Dto\StoreSemesterDto;
use App\Domain\Dto\UpdateSemesterDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\Semester;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class SemesterRepository implements SemesterRepositoryInterface
{
    private const DEFAULT_PER_PAGE = 15;

    public function findById(int $id): ?Semester
    {
        return Semester::find($id);
    }

    public function findOrFail(int $id): Semester
    {
        return Semester::findOrFail($id);
    }

    public function getAll(?string $searchTerm = null, int $perPage = self::DEFAULT_PER_PAGE, string $sort = 'start_year', string $direction = 'desc'): LengthAwarePaginator
    {
        return Semester::query()
            ->when($searchTerm, function ($query, $searchTerm): void {
                $query->where(function ($q) use ($searchTerm): void {
                    $q->where('start_year', 'like', "%{$searchTerm}%")
                        ->orWhere('semester_start_date', 'like', "%{$searchTerm}%")
                        ->orWhere('session_start_date', 'like', "%{$searchTerm}%")
                        ->orWhere('end_date', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage);
    }

    public function getAllForSelect(): Collection
    {
        return Semester::orderBy('start_year', 'desc')->get();
    }

    public function create(StoreSemesterDto $data): Semester
    {
        return Semester::create([
            'start_year' => $data->start_year,
            'season' => $data->season,
            'semester_start_date' => $data->semester_start_date,
            'session_start_date' => $data->session_start_date,
            'end_date' => $data->end_date,
        ]);
    }

    public function update(int $id, UpdateSemesterDto $data): ?Semester
    {
        $semester = $this->findById($id);

        if ($semester) {
            $semester->update([
                'start_year' => $data->start_year,
                'season' => $data->season,
                'semester_start_date' => $data->semester_start_date,
                'session_start_date' => $data->session_start_date,
                'end_date' => $data->end_date,
            ]);

            return $semester->fresh();
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $semester = $this->findById($id);

        if ($semester) {
            return $semester->delete();
        }

        return false;
    }

    public function findByYearAndSeason(int $startYear, string $season): ?Semester
    {
        return Semester::where('start_year', $startYear)
            ->where('season', $season)
            ->first();
    }
}
