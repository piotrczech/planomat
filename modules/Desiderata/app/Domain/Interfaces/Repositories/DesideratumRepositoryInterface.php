<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Interfaces\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

interface DesideratumRepositoryInterface
{
    public function getDesideratumForUserAndSemester(int $userId, int $semesterId): ?Desideratum;

    public function findByScientificWorkerAndSemester(int $workerId, int $semesterId): ?UpdateOrCreateDesideratumDto;

    public function updateOrCreate(UpdateOrCreateDesideratumDto $dto, User $user, int $semesterId): Desideratum;

    public function getLastUpdateDate(int $userId): ?string;

    public function getAllDesiderataForPdfExport(int $semesterId): Collection;

    public function getScientificWorkersWithoutDesiderata(int $semesterId): Collection;
}
