<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\ActivityLog\UseCases\StoreActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class DeleteSessionConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly StoreActivityLogUseCase $storeActivityLogUseCase,
    ) {
    }

    public function execute(int $consultationId): bool
    {
        $scientificWorkerId = Auth::id();

        $deleted = $this->consultationRepository->deleteSessionConsultation(
            $consultationId,
            $scientificWorkerId,
        );

        if ($deleted) {
            $this->storeActivityLogUseCase->execute(
                new StoreActivityLogDto(
                    userId: (string) $scientificWorkerId,
                    module: ActivityLogModuleEnum::CONSULTATION->value,
                    action: ActivityLogActionEnum::DELETE->value,
                ),
            );
        }

        return $deleted;
    }
}
