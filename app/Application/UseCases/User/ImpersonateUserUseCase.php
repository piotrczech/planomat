<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Lab404\Impersonate\Services\ImpersonateManager;

final class ImpersonateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ImpersonateManager $impersonateManager,
    ) {
    }

    public function execute(int $userIdToImpersonate): bool
    {
        /** @var User|null $adminUser */
        $adminUser = Auth::user();
        $userToImpersonate = $this->userRepository->findById($userIdToImpersonate);

        if (!$adminUser) {
            Log::warning('[ImpersonateUserUseCase] Admin user not found.');

            return false;
        }

        if (!$userToImpersonate) {
            Log::warning('[ImpersonateUserUseCase] User to impersonate not found with ID: ' . $userIdToImpersonate);

            return false;
        }

        Log::info('[ImpersonateUserUseCase] Attempting impersonation.', [
            'admin_user_id' => $adminUser->id,
            'admin_user_role' => $adminUser->role->value,
            'user_to_impersonate_id' => $userToImpersonate->id,
            'user_to_impersonate_role' => $userToImpersonate->role->value,
        ]);

        if ($adminUser->is($userToImpersonate)) {
            Log::info('[ImpersonateUserUseCase] Admin user tried to impersonate self.');

            return false;
        }

        $canAdminImpersonate = $adminUser->canImpersonate();
        $canTargetBeImpersonated = $userToImpersonate->canBeImpersonated();

        Log::info('[ImpersonateUserUseCase] Permission checks.', [
            'admin_can_impersonate' => $canAdminImpersonate,
            'target_can_be_impersonated' => $canTargetBeImpersonated,
        ]);

        if (!$canAdminImpersonate || !$canTargetBeImpersonated) {
            Log::warning('[ImpersonateUserUseCase] Impersonation check failed.', [
                'admin_can_impersonate' => $canAdminImpersonate,
                'target_can_be_impersonated' => $canTargetBeImpersonated,
            ]);

            return false;
        }

        if ($this->impersonateManager->isImpersonating()) {
            Log::info('[ImpersonateUserUseCase] Previous impersonation found, leaving it.');
            $this->impersonateManager->leave();
        }

        Log::info('[ImpersonateUserUseCase] Taking impersonation...');
        $this->impersonateManager->take($adminUser, $userToImpersonate);
        Log::info('[ImpersonateUserUseCase] Impersonation successful.');

        return true;
    }

    public function leaveImpersonation(): bool
    {
        if (!$this->impersonateManager->isImpersonating()) {
            Log::info('[ImpersonateUserUseCase] No active impersonation to leave.');

            return false;
        }

        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if ($currentUser && method_exists($currentUser, 'leaveImpersonation') && $this->impersonateManager->isImpersonating()) {
            Log::info('[ImpersonateUserUseCase] Leaving impersonation via User model method.');
            $currentUser->leaveImpersonation();

            return true;
        }

        if ($this->impersonateManager->isImpersonating()) {
            Log::info('[ImpersonateUserUseCase] Leaving impersonation via ImpersonateManager directly (fallback).');
            $this->impersonateManager->leave();

            return true;
        }

        Log::warning('[ImpersonateUserUseCase] Failed to leave impersonation for an unknown reason.');

        return false;
    }
}
