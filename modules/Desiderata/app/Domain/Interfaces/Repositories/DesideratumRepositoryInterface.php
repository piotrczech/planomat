<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Interfaces\Repositories;

use Illuminate\Support\Collection;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

interface DesideratumRepositoryInterface
{
    public function findByScientificWorkerAndSemester(
        int $workerId,
        int $semesterId,
    ): ?UpdateOrCreateDesideratumDto;

    public function updateOrCreate(UpdateOrCreateDesideratumDto $dto): Desideratum;

    public function getLastUpdateDateByScientificWorker(int $workerId): ?string;

    public function getAllDesiderataForPdfExport(): Collection;
}
