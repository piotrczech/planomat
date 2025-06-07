<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Domain\User\Dto\StoreUserDto;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\User;

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
