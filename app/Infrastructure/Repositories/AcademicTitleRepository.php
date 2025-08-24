<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\AcademicTitleRepositoryInterface;
use App\Infrastructure\Models\AcademicTitle;
use Illuminate\Support\Collection;

final class AcademicTitleRepository implements AcademicTitleRepositoryInterface
{
    public function listAllTitles(): Collection
    {
        return AcademicTitle::orderBy('title')->pluck('title');
    }
}
