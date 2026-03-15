<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepositoryInterface;

final class SetUserActiveUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId, bool $isActive): bool
    {
        return $this->userRepository->setActive($userId, $isActive);
    }
}
