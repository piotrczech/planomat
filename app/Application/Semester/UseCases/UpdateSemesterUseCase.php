<?php

declare(strict_types=1);

namespace App\Application\Semester\UseCases;

use App\Domain\Semester\Dto\UpdateSemesterDto;
use App\Domain\Semester\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;

final readonly class UpdateSemesterUseCase
{
    public function __construct(private SemesterRepositoryInterface $semesterRepository)
    {
    }

    public function execute(int $id, UpdateSemesterDto $dto): ?Semester
    {
        // Logika biznesowa specyficzna dla aktualizacji (np. zdarzenia) może być tutaj.
        // Walidacja jest obsługiwana przez UpdateSemesterDto.
        return $this->semesterRepository->update($id, $dto);
    }
}
