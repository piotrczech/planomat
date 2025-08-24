<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use Illuminate\Support\Collection;

interface AcademicTitleRepositoryInterface
{
    public function listAllTitles(): Collection;
}
