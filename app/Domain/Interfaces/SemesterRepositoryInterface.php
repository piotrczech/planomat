<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Dto\StoreSemesterDto;
use App\Domain\Dto\UpdateSemesterDto;
use App\Infrastructure\Models\Semester;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SemesterRepositoryInterface
{
    public function findById(int $id): ?Semester;

    public function findOrFail(int $id): Semester;

    public function getAll(?string $searchTerm = null, int $perPage = 15, string $sort = 'start_year', string $direction = 'desc'): LengthAwarePaginator;

    public function getAllForSelect(): Collection;

    public function create(StoreSemesterDto $data): Semester;

    public function update(int $id, UpdateSemesterDto $data): ?Semester;

    public function delete(int $id): bool;

    public function findByYearAndSeason(int $startYear, string $season): ?Semester;
}
