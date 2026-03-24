<?php

declare(strict_types=1);

namespace Modules\Desiderata\Domain\Interfaces\Repositories;

use App\Infrastructure\Models\User;
use Illuminate\Support\Collection;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

interface DesideratumRepositoryInterface
{
    public function getDesideratumForUserAndSemester(int $userId, int $semesterId): ?Desideratum;

    public function findByScientificWorkerAndSemester(int $workerId, int $semesterId): ?UpdateOrCreateDesideratumDto;

    public function updateOrCreate(UpdateOrCreateDesideratumDto $dto, User $user, int $semesterId): Desideratum;

    public function getLastUpdateDate(int $userId, int $semesterId): ?string;

    public function countScientificWorkersForPdfExport(int $semesterId, bool $excludeInactiveForActiveSemester = false): int;

    public function getAllDesiderataForPdfExport(int $semesterId, bool $excludeInactiveForActiveSemester = false): Collection;

    public function getDesiderataForPdfExportChunked(int $semesterId, int $chunkSize, callable $callback, bool $excludeInactiveForActiveSemester = false): void;

    public function getScientificWorkersWithoutDesiderata(int $semesterId, bool $excludeInactiveForActiveSemester = false): Collection;

    public function findLatestByScientificWorkerBeforeSemester(int $workerId, int $currentSemesterId): ?UpdateOrCreateDesideratumDto;
}
