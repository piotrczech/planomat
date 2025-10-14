<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Application\UseCases\ActivityLog\CreateActivityLogUseCase;
use App\Domain\Dto\StoreActivityLogDto;
use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Illuminate\Support\Facades\Auth;
use Modules\Consultation\Domain\Interfaces\Repositories\ConsultationRepositoryInterface;

final class DeletePartTimeConsultationUseCase
{
    public function __construct(
        private readonly ConsultationRepositoryInterface $consultationRepository,
        private readonly CreateActivityLogUseCase $createActivityLogUseCase,
    ) {
    }

    public function execute(int $consultationId): bool
    {
        $scientificWorkerId = Auth::id();

        $deleted = $this->consultationRepository->deletePartTimeConsultation(
            $consultationId,
            $scientificWorkerId,
        );

        if ($deleted) {
            $this->createActivityLogUseCase->execute(
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
