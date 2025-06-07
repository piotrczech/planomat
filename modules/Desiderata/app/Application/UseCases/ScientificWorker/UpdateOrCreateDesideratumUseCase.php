<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Domain\ActivityLog\Dto\StoreActivityLogDto;
use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

class UpdateOrCreateDesideratumUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
        private readonly StoreActivityLogUseCase $storeActivityLogUseCase,
    ) {
    }

    public function execute(UpdateOrCreateDesideratumDto $dto): Desideratum
    {
        $desideratum = $this->desideratumRepository->updateOrCreate($dto);

        $action = $desideratum->wasRecentlyCreated ? ActivityLogActionEnum::CREATE : ActivityLogActionEnum::UPDATE;

        $this->storeActivityLogUseCase->execute(
            new StoreActivityLogDto(
                userId: (string) Auth::id(),
                module: ActivityLogModuleEnum::DESIDERATA->value,
                action: $action->value,
            ),
        );

        return $desideratum;
    }
}
