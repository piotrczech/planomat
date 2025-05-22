<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Repository;

use Illuminate\Database\Eloquent\Collection;

interface DesideratumRepositoryInterface
{
    public function getAllDesiderataForPdfExport(): Collection;
}
