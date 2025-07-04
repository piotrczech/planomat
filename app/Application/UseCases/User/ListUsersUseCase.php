<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepositoryInterface;
use App\Domain\Enums\RoleEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListUsersUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(
        string $search = '',
        int $perPage = 15,
        ?RoleEnum $filterRole = RoleEnum::SCIENTIFIC_WORKER,
    ): LengthAwarePaginator {
        return $this->userRepository->getAllPaginated($search, $perPage, $filterRole);
    }
}
