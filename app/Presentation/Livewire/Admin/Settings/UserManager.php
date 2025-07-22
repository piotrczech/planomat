<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\User\DeleteUserUseCase;
use App\Application\UseCases\User\ImpersonateUserUseCase;
use App\Application\UseCases\User\ListUsersUseCase;
use App\Domain\Enums\RoleEnum;
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

    public bool $showDeleteConfirmationModal = false;

    public ?int $editingUserId = null;

    public ?int $deletingUserId = null;

    public string $userSearch = '';

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

    #[On('deleteUserConfirmed')]
    public function handleDeleteUserConfirmed(DeleteUserUseCase $deleteUserUseCase): void
    {
        if ($this->deletingUserId) {
            if ($this->deletingUserId === Auth::id()) {
                $this->dispatch('notify', title: __('admin_settings.users.notifications.cannot_delete_self_title'), message: __('admin_settings.users.notifications.cannot_delete_self_message'), type: 'error');
                $this->closeDeleteConfirmationModal();

                return;
            }
            $deleteUserUseCase->execute($this->deletingUserId);
            $this->closeDeleteConfirmationModal();
            $this->dispatch('notify', title: __('admin_settings.users.notifications.user_deleted_title'), message: __('admin_settings.users.notifications.user_deleted_message'), type: 'success');
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

    public function openDeleteConfirmationModal(int $userId): void
    {
        $this->deletingUserId = $userId;
        $this->showDeleteConfirmationModal = true;
    }

    #[On('closeDeleteConfirmationModal')]
    public function closeDeleteConfirmationModal(): void
    {
        $this->deletingUserId = null;
        $this->showDeleteConfirmationModal = false;
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
        $users = $listUsersUseCase->execute($this->userSearch, $this->perPage, $this->filterRole);

        return view('livewire.admin.settings.user-manager', [
            'users' => $users,
        ]);
    }
}
