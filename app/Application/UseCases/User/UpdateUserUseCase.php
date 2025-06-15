<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Dto\UpdateUserDto;
use App\Domain\Interfaces\UserRepositoryInterface;

final class UpdateUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(UpdateUserDto $userData): bool
    {
        return $this->userRepository->update($userData);
    }
}
