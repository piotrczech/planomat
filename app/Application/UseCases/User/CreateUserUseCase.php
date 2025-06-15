<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Dto\StoreUserDto;
use App\Domain\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Models\User;

final class CreateUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(StoreUserDto $userData): User
    {
        return $this->userRepository->create($userData);
    }
}
