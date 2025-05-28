<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListSemestersUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(?string $searchTerm = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->semesterRepository->getAll($searchTerm, $perPage);
    }
}
