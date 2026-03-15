<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Dto\StoreUserDto;
use App\Domain\Dto\UpdateUserDto;
use App\Domain\Enums\RoleEnum;
use App\Domain\Enums\UserListViewEnum;
use App\Infrastructure\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getAllPaginated(
        string $search = '',
        int $perPage = 15,
        ?RoleEnum $filterRole = RoleEnum::SCIENTIFIC_WORKER,
        UserListViewEnum $viewFilter = UserListViewEnum::ACTIVE,
    ): LengthAwarePaginator;

    public function findById(int $userId, bool $withTrashed = false): ?User;

    public function create(StoreUserDto $userData): User;

    public function update(UpdateUserDto $userData): bool;

    public function delete(int $userId): bool;

    public function archive(int $userId): bool;

    public function restore(int $userId): bool;

    public function setActive(int $userId, bool $isActive): bool;

    public function findByEmail(string $email): ?User;
}
