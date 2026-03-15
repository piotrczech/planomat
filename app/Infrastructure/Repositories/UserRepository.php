<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Dto\StoreUserDto;
use App\Domain\Dto\UpdateUserDto;
use App\Domain\Interfaces\UserRepositoryInterface;
use App\Domain\Enums\RoleEnum;
use App\Domain\Enums\UserListViewEnum;
use App\Infrastructure\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

final class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginated(
        string $search = '',
        int $perPage = 15,
        ?RoleEnum $filterRole = RoleEnum::SCIENTIFIC_WORKER,
        UserListViewEnum $viewFilter = UserListViewEnum::ACTIVE,
    ): LengthAwarePaginator {
        $query = match ($viewFilter) {
            UserListViewEnum::ARCHIVED => User::onlyTrashed(),
            UserListViewEnum::ACTIVE => User::query(),
        };

        return $query
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search): void {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('academic_title', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filterRole, function ($query, $filterRole) {
                return $query->where('role', $filterRole);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage);
    }

    public function findById(int $userId, bool $withTrashed = false): ?User
    {
        return $withTrashed
            ? User::withTrashed()->find($userId)
            : User::find($userId);
    }

    public function create(StoreUserDto $userData): User
    {
        return User::create([
            'academic_title' => $userData->academic_title,
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
            'role' => $userData->role,
            'is_active' => true,
        ]);
    }

    public function update(UpdateUserDto $userData): bool
    {
        $user = User::find($userData->id);

        if (!$user) {
            return false;
        }

        $attributes = [
            'academic_title' => $userData->academic_title,
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
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
        return $this->archive($userId);
    }

    public function archive(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        $baseEmail = User::stripArchiveSuffixFromEmail($user->email);
        $archiveIndex = 1;

        while (true) {
            $candidateEmail = User::formatArchivedEmail($baseEmail, $archiveIndex);

            $exists = User::withTrashed()
                ->where('email', $candidateEmail)
                ->exists();

            if (!$exists) {
                break;
            }

            $archiveIndex++;
        }

        return DB::transaction(function () use ($user, $candidateEmail): bool {
            $user->forceFill([
                'email' => $candidateEmail,
                'usos_id' => null,
                'is_active' => false,
            ])->save();

            $this->revokeUserSessions($user->id);

            return (bool) $user->delete();
        });
    }

    public function restore(int $userId): bool
    {
        /** @var User|null $user */
        $user = User::withTrashed()->find($userId);

        if (!$user || !$user->trashed()) {
            return false;
        }

        if (!$user->canBeRestoredWithinWindow()) {
            throw new RuntimeException((string) __('admin_settings.users.notifications.user_restore_window_expired_message'));
        }

        $baseEmail = User::stripArchiveSuffixFromEmail($user->email);
        $emailTaken = User::where('email', $baseEmail)->exists();

        if ($emailTaken) {
            throw new RuntimeException((string) __('admin_settings.users.notifications.user_restore_email_taken_message'));
        }

        return DB::transaction(function () use ($user, $baseEmail): bool {
            $user->restore();

            return (bool) $user->forceFill([
                'email' => $baseEmail,
                'is_active' => true,
            ])->save();
        });
    }

    public function setActive(int $userId, bool $isActive): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        $updated = $user->update([
            'is_active' => $isActive,
        ]);

        if ($updated && !$isActive) {
            $this->revokeUserSessions($user->id);
        }

        return $updated;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    private function revokeUserSessions(int $userId): void
    {
        DB::table((string) config('session.table', 'sessions'))
            ->where('user_id', $userId)
            ->delete();
    }
}
