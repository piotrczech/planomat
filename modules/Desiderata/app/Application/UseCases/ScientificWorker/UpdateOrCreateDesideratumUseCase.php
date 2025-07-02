<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\ScientificWorker;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Application\UseCases\Semester\GetActiveDesiderataSemesterUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use Exception;

class UpdateOrCreateDesideratumUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly CreateActivityLogUseCase $createActivityLogUseCase,
        private readonly GetActiveDesiderataSemesterUseCase $getActiveDesiderataSemesterUseCase,
    ) {
    }

    public function execute(UpdateOrCreateDesideratumDto $dto): Desideratum
    {
        $user = Auth::user();
        $currentSemester = $this->getActiveDesiderataSemesterUseCase->execute();

        if (!$user || !$currentSemester) {
            throw new Exception('Cannot create or update desideratum without a user or an active semester.');
        }

        $desideratum = $this->desideratumRepository->updateOrCreate(
            $dto,
            $user,
            $currentSemester->id,
        );

        $action = $desideratum->wasRecentlyCreated ? ActivityLogActionEnum::CREATE : ActivityLogActionEnum::UPDATE;

        $this->createActivityLogUseCase->execute(
            new StoreActivityLogDto(
                userId: (string) $user->id,
                module: ActivityLogModuleEnum::DESIDERATA->value,
                action: $action->value,
            ),
        );

        return $desideratum;
    }
}
