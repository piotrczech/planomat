<?php

declare(strict_types=1);

namespace Modules\Desiderata\Infrastructure\Authorization;

use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User;
use Modules\Desiderata\Infrastructure\Models\Desideratum;
use Illuminate\Auth\Access\HandlesAuthorization;

class DesideratumPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Desideratum $desideratum): bool
    {
        return $user->id === $desideratum->user_id || $user->hasRole(RoleEnum::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleEnum::SCIENTIFIC_WORKER);
    }

    public function update(User $user, Desideratum $desideratum): bool
    {
        return ($user->id === $desideratum->user_id && $user->hasRole(RoleEnum::SCIENTIFIC_WORKER)) ||
               $user->hasRole(RoleEnum::ADMIN);
    }

    public function delete(User $user, Desideratum $desideratum): bool
    {
        return ($user->id === $desideratum->user_id && $user->hasRole(RoleEnum::SCIENTIFIC_WORKER)) ||
               $user->hasRole(RoleEnum::ADMIN);
    }
}
