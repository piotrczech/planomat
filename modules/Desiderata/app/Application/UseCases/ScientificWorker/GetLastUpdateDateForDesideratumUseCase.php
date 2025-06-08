<?php

declare(strict_types=1);

namespace Modules\Desiderata\Application\UseCases\ScientificWorker;

use Modules\Desiderata\Domain\Interfaces\Repositories\DesideratumRepositoryInterface;
use Illuminate\Support\Facades\Auth;

final class GetLastUpdateDateForDesideratumUseCase
{
    public function __construct(
        private readonly DesideratumRepositoryInterface $desideratumRepository,
    ) {
    }

    public function execute(): ?string
    {
        $scientificWorkerId = Auth::id();

        if (!$scientificWorkerId) {
            return null;
        }

        return $this->desideratumRepository->getLastUpdateDate(
            $scientificWorkerId,
        );
    }
}
