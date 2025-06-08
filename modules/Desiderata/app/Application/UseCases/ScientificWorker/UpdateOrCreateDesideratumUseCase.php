<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Application\Semester\UseCases\GetCurrentSemesterUseCase;
use App\Domain\ActivityLog\Dto\StoreActivityLogDto;
use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use Exception;

class UpdateOrCreateDesideratumUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly StoreActivityLogUseCase $storeActivityLogUseCase,
        private readonly GetCurrentSemesterUseCase $getCurrentSemesterUseCase,
    ) {
    }

    public function execute(UpdateOrCreateDesideratumDto $dto): Desideratum
    {
        $user = Auth::user();
        $currentSemester = $this->getCurrentSemesterUseCase->execute();

        if (!$user || !$currentSemester) {
            // Rzucenie wyjątku jest lepsze w tym przypadku, bo to sytuacja, która nie powinna mieć miejsca
            throw new Exception('Cannot create or update desideratum without a user or an active semester.');
        }

        $desideratum = $this->desideratumRepository->updateOrCreate(
            $dto,
            $user,
            $currentSemester->id,
        );

        $action = $desideratum->wasRecentlyCreated ? ActivityLogActionEnum::CREATE : ActivityLogActionEnum::UPDATE;

        $this->storeActivityLogUseCase->execute(
            new StoreActivityLogDto(
                userId: (string) $user->id,
                module: ActivityLogModuleEnum::DESIDERATA->value,
                action: $action->value,
            ),
        );

        return $desideratum;
    }
}
