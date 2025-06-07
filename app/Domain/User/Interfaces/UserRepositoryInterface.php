<?php

declare(strict_types=1);

namespace App\Domain\User\Interfaces;

use App\Domain\User\Dto\StoreUserDto;
use App\Domain\User\Dto\UpdateUserDto;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getAllPaginated(
        string $search = '',
        int $perPage = 15,
        ?RoleEnum $filterRole = RoleEnum::SCIENTIFIC_WORKER,
    ): LengthAwarePaginator;

    public function findById(int $userId): ?User;

    public function create(StoreUserDto $userData): User;

    public function update(UpdateUserDto $userData): bool;

    public function delete(int $userId): bool;
}
