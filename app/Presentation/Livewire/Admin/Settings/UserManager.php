<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\User\ArchiveUserUseCase;
use App\Application\UseCases\User\GetUserUseCase;
use App\Application\UseCases\User\ImpersonateUserUseCase;
use App\Application\UseCases\User\ListUsersUseCase;
use App\Application\UseCases\User\RestoreUserUseCase;
use App\Application\UseCases\User\SetUserActiveUseCase;
use App\Domain\Enums\RoleEnum;
use App\Domain\Enums\UserListViewEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Exception;

class UserManager extends Component
{
    use WithPagination;

    public int $perPage = 25;

    public array $perPageOptions = [10, 25, 50];

    public bool $showCreateUserModal = false;

    public bool $showEditUserModal = false;

    public bool $showArchiveConfirmationModal = false;

    public bool $showRestoreErrorModal = false;

    public string $restoreErrorMessage = '';

    public ?int $editingUserId = null;

    public ?int $archivingUserId = null;

    public string $userSearch = '';

    public string $viewFilter = UserListViewEnum::ACTIVE->value;

    public RoleEnum $filterRole;

    public function mount(RoleEnum $filterRole = RoleEnum::SCIENTIFIC_WORKER): void
    {
        $this->filterRole = $filterRole;
    }

    public function updatedUserSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[On('userSaved')]
    public function handleUserSaved(): void
    {
        if ($this->showCreateUserModal) {
            $this->showCreateUserModal = false;
        } elseif ($this->showEditUserModal) {
            $this->showEditUserModal = false;
            $this->editingUserId = null;
        }
        $this->dispatch('notify', title: __('admin_settings.users.notifications.user_saved_title'), message: __('admin_settings.users.notifications.user_saved_message'), type: 'success');
        $this->resetPage();
    }

    #[On('archiveUserConfirmed')]
    public function handleArchiveUserConfirmed(ArchiveUserUseCase $archiveUserUseCase): void
    {
        if ($this->archivingUserId) {
            if ($this->archivingUserId === Auth::id()) {
                $this->dispatch('notify', title: __('admin_settings.users.notifications.cannot_archive_self_title'), message: __('admin_settings.users.notifications.cannot_archive_self_message'), type: 'error');
                $this->closeArchiveConfirmationModal();

                return;
            }

            $archived = $archiveUserUseCase->execute($this->archivingUserId);
            $this->closeArchiveConfirmationModal();

            if (!$archived) {
                $this->dispatch('notify', title: __('admin_settings.users.notifications.user_status_update_failed_title'), message: __('admin_settings.users.notifications.user_status_update_failed_message'), type: 'error');

                return;
            }

            $this->dispatch('notify', title: __('admin_settings.users.notifications.user_archived_title'), message: __('admin_settings.users.notifications.user_archived_message'), type: 'success');
            $this->resetPage();
        }
    }

    public function openCreateUserModal(): void
    {
        $this->editingUserId = null;
        $this->resetErrorBag();
        $this->showEditUserModal = false;
        $this->showCreateUserModal = true;
    }

    public function openEditUserModal(int $userId): void
    {
        $this->editingUserId = $userId;
        $this->resetErrorBag();
        $this->showCreateUserModal = false;
        $this->showEditUserModal = true;
    }

    #[On('closeUserFormModal')]
    public function closeUserFormModal(): void
    {
        if ($this->showCreateUserModal) {
            $this->showCreateUserModal = false;
        }

        if ($this->showEditUserModal) {
            $this->showEditUserModal = false;
            $this->editingUserId = null;
        }
    }

    public function openArchiveConfirmationModal(int $userId): void
    {
        $this->archivingUserId = $userId;
        $this->showArchiveConfirmationModal = true;
    }

    #[On('closeArchiveConfirmationModal')]
    public function closeArchiveConfirmationModal(): void
    {
        $this->archivingUserId = null;
        $this->showArchiveConfirmationModal = false;
    }

    public function switchViewFilter(string $filter): void
    {
        $parsedFilter = UserListViewEnum::tryFrom($filter);

        if (!$parsedFilter) {
            return;
        }

        $this->viewFilter = $parsedFilter->value;
        $this->resetPage();
    }

    public function restoreUser(int $userId, RestoreUserUseCase $restoreUserUseCase): void
    {
        try {
            $restored = $restoreUserUseCase->execute($userId);

            if (!$restored) {
                $this->openRestoreErrorModal((string) __('admin_settings.users.notifications.user_restore_failed_message'));

                return;
            }

            $this->dispatch('notify', title: __('admin_settings.users.notifications.user_restored_title'), message: __('admin_settings.users.notifications.user_restored_message'), type: 'success');
            $this->resetPage();
        } catch (Exception $e) {
            $this->openRestoreErrorModal($e->getMessage());
        }
    }

    public function closeRestoreErrorModal(): void
    {
        $this->showRestoreErrorModal = false;
        $this->restoreErrorMessage = '';
    }

    public function toggleUserActive(int $userId, SetUserActiveUseCase $setUserActiveUseCase, GetUserUseCase $getUserUseCase): void
    {
        if ($userId === Auth::id()) {
            $this->dispatch('notify', title: __('admin_settings.users.notifications.cannot_suspend_self_title'), message: __('admin_settings.users.notifications.cannot_suspend_self_message'), type: 'error');

            return;
        }

        $user = $getUserUseCase->execute($userId);

        if (!$user) {
            $this->dispatch('notify', title: __('admin_settings.users.notifications.user_status_update_failed_title'), message: __('admin_settings.users.notifications.user_status_update_failed_message'), type: 'error');

            return;
        }

        $updated = $setUserActiveUseCase->execute($userId, !$user->is_active);

        if (!$updated) {
            $this->dispatch('notify', title: __('admin_settings.users.notifications.user_status_update_failed_title'), message: __('admin_settings.users.notifications.user_status_update_failed_message'), type: 'error');

            return;
        }

        $this->dispatch('notify', title: __('admin_settings.users.notifications.user_status_updated_title'), message: __('admin_settings.users.notifications.user_status_updated_message'), type: 'success');
    }

    public function impersonateUser(int $userIdToImpersonate, ImpersonateUserUseCase $impersonateUserUseCase): void
    {
        $currentAuthId = Auth::id();

        if (!$currentAuthId) {
            $this->dispatch('notify', title: __('Error'), message: __('Authenticated user not found.'), type: 'error');

            return;
        }

        if ($userIdToImpersonate === $currentAuthId) {
            $this->dispatch('notify', title: __('admin_settings.users.notifications.cannot_impersonate_self_title'), message: __('admin_settings.users.notifications.cannot_impersonate_self_message'), type: 'error');

            return;
        }

        try {
            $success = $impersonateUserUseCase->execute($userIdToImpersonate);

            if ($success) {
                $this->redirect(route('dashboard'), navigate: true);
            } else {
                $this->dispatch('notify', title: __('admin_settings.users.notifications.impersonation_failed_title'), message: __('admin_settings.users.notifications.impersonation_failed_message'), type: 'error');
            }
        } catch (Exception $e) {
            report($e);
            $this->dispatch('notify', title: __('admin_settings.users.notifications.impersonation_error_title'), message: __('admin_settings.users.notifications.impersonation_error_admin_message'), type: 'error');
        }
    }

    public function render(ListUsersUseCase $listUsersUseCase): View
    {
        $parsedFilter = UserListViewEnum::tryFrom($this->viewFilter) ?? UserListViewEnum::ACTIVE;

        $users = $listUsersUseCase->execute(
            search: $this->userSearch,
            perPage: $this->perPage,
            filterRole: $this->filterRole,
            viewFilter: $parsedFilter,
        );

        return view('livewire.admin.settings.user-manager', [
            'users' => $users,
            'isArchivedView' => $parsedFilter === UserListViewEnum::ARCHIVED,
        ]);
    }

    private function openRestoreErrorModal(string $message): void
    {
        $this->restoreErrorMessage = $message;
        $this->showRestoreErrorModal = true;
    }
}
