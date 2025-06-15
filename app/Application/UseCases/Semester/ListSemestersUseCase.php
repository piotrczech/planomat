<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Interfaces\SemesterRepositoryInterface;
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
