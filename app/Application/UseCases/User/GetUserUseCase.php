<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Models\User;

final class GetUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }
}
