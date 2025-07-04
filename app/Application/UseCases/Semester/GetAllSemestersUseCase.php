<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Interfaces\SemesterRepositoryInterface;
use Illuminate\Support\Collection;

final readonly class GetAllSemestersUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(): Collection
    {
        return $this->semesterRepository->getAllForSelect();
    }
}
