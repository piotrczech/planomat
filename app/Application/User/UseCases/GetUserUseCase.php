<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\User;

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
