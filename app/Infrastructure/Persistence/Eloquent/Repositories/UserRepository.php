<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Dto\StoreUserDto;
use App\Domain\User\Dto\UpdateUserDto;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

final class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginated(
        string $search = '',
        int $perPage = 15,
        ?RoleEnum $filterRole = RoleEnum::SCIENTIFIC_WORKER,
    ): LengthAwarePaginator {
        return User::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filterRole, function ($query, $filterRole) {
                return $query->where('role', $filterRole);
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $userId): ?User
    {
        return User::find($userId);
    }

    public function create(StoreUserDto $userData): User
    {
        return User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
            'role' => $userData->role,
        ]);
    }

    public function update(UpdateUserDto $userData): bool
    {
        $user = User::find($userData->id);

        if (!$user) {
            return false;
        }

        $attributes = [
            'name' => $userData->name,
            'email' => $userData->email,
            'role' => $userData->role,
        ];

        if ($userData->password) {
            $attributes['password'] = Hash::make($userData->password);
        }

        return $user->update($attributes);
    }

    public function delete(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }
}
