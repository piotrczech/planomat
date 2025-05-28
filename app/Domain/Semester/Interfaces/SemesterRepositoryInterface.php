<?php

declare(strict_types=1);

namespace App\Domain\Semester\Interfaces;

use App\Domain\Semester\Dto\StoreSemesterDto;
use App\Domain\Semester\Dto\UpdateSemesterDto;
use App\Models\Semester;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SemesterRepositoryInterface
{
    public function findById(int $id): ?Semester;

    public function getAll(?string $searchTerm = null, int $perPage = 15): LengthAwarePaginator;

    public function create(StoreSemesterDto $data): Semester;

    public function update(int $id, UpdateSemesterDto $data): ?Semester;

    public function delete(int $id): bool;

    public function findByYearAndSeason(int $startYear, string $season): ?Semester;
}
