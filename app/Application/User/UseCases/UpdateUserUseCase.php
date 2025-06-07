<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Domain\User\Dto\UpdateUserDto;
use App\Domain\User\Interfaces\UserRepositoryInterface;

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
