<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Dto\StoreSemesterDto;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;

final readonly class CreateSemesterUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(StoreSemesterDto $dto): Semester
    {
        // Logika biznesowa specyficzna dla tworzenia semestru (np. zdarzenia) może być tutaj.
        // Walidacja jest obsługiwana przez StoreSemesterDto.
        return $this->semesterRepository->create($dto);
    }
}
