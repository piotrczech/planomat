<?php

declare(strict_types=1);

namespace App\Application\UseCases\AcademicTitle;

use App\Domain\Interfaces\AcademicTitleRepositoryInterface;
use Illuminate\Support\Collection;

final class ListAcademicTitlesUseCase
{
    public function __construct(private readonly AcademicTitleRepositoryInterface $repository)
    {
    }

    public function execute(): Collection
    {
        return $this->repository->listAllTitles();
    }
}
