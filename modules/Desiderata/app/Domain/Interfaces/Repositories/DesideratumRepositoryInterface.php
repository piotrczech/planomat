<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Interfaces\Repositories;

use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;

interface DesideratumRepositoryInterface
{
    public function findByScientificWorkerAndSemester(
        int $workerId,
        int $semesterId,
    ): ?UpdateOrCreateDesideratumDto;

    public function updateOrCreate(UpdateOrCreateDesideratumDto $dto): int;

    public function getLastUpdateDateByScientificWorker(int $workerId): ?string;
}
