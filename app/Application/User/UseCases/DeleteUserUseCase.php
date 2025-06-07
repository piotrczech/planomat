<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Domain\User\Interfaces\UserRepositoryInterface;

final class DeleteUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }
}
