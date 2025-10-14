<?php

declare(strict_types=1);

namespace Modules\Consultation\Application\UseCases\ScientificWorker;

use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User;
use Illuminate\Support\Collection;

final readonly class GetScientificWorkersListUseCase
{
    public function execute(): Collection
    {
        return User::where('role', RoleEnum::SCIENTIFIC_WORKER)
            ->whereNull('deleted_at')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'academic_title']);
    }
}
