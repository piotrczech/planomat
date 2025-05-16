<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\ScientificWorker;

use Modules\Desiderata\Domain\Dto\UpdateOrCreateDesideratumDto;
use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;

class UpdateOrCreateDesideratumUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
    ) {
    }

    public function execute(UpdateOrCreateDesideratumDto $dto): int
    {
        return $this->desideratumRepository->updateOrCreate($dto);
    }
}
