<?php

declare(strict_types=1);

namespace App\Application\UseCases\Semester;

use App\Domain\Dto\StoreSemesterDto;
use App\Domain\Interfaces\SemesterRepositoryInterface;
use App\Infrastructure\Models\Semester;

final readonly class CreateSemesterUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(StoreSemesterDto $dto): Semester
    {
        return $this->semesterRepository->create($dto);
    }
}
