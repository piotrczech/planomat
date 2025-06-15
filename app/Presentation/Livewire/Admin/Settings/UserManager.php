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

    public function mount(): void
    {
        // Tutaj można dodać logikę inicjalizacji, jeśli jest potrzebna
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
        // dump('UserManager handleUserSaved CALLED', ['showCreateUserModal' => $this->showCreateUserModal, 'showEditUserModal' => $this->showEditUserModal, 'editingUserId' => $this->editingUserId]);

        if ($this->showCreateUserModal) {
            // dump('UserManager handleUserSaved: Closing CREATE modal');
            $this->showCreateUserModal = false;
        } elseif ($this->showEditUserModal) {
            // dump('UserManager handleUserSaved: Closing EDIT modal for userId: ' . $this->editingUserId);
            $this->showEditUserModal = false;
            $this->editingUserId = null;
        }
        $this->dispatch('notify', title: __('admin_settings.users.notifications.user_saved_title'), message: __('admin_settings.users.notifications.user_saved_message'), type: 'success');
        $this->resetPage(); // Odśwież listę użytkowników
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
        // Sprawdzenie, czy użytkownik nie próbuje impersonifikować samego siebie
        // $adminUserId jest tutaj nadal potrzebne do tego sprawdzenia, ale nie jest przekazywane do UseCase.
        $currentAuthId = Auth::id();

        if (!$currentAuthId) {
            // Ten przypadek jest mało prawdopodobny, jeśli metoda jest wywoływana przez zalogowanego użytkownika
            $this->dispatch('notify', title: __('Error'), message: __('Authenticated user not found.'), type: 'error');

            return;
        }

        if ($userIdToImpersonate === $currentAuthId) {
            $this->dispatch('notify', title: __('admin_settings.users.notifications.cannot_impersonate_self_title'), message: __('admin_settings.users.notifications.cannot_impersonate_self_message'), type: 'error');

            return;
        }

        try {
            // Wywołanie UseCase z poprawną liczbą argumentów
            $success = $impersonateUserUseCase->execute($userIdToImpersonate);

            if ($success) {
                // Przekierowanie na dashboard po udanej impersonifikacji
                $this->redirect(route('dashboard'), navigate: true);
            } else {
                // Ten blok może nie być osiągnięty, jeśli UseCase rzuca wyjątki lub warunki (canImpersonate/canBeImpersonated) są false
                $this->dispatch('notify', title: __('admin_settings.users.notifications.impersonation_failed_title'), message: __('admin_settings.users.notifications.impersonation_failed_message'), type: 'error');
            }
        } catch (Exception $e) {
            report($e); // Logowanie błędu
            $this->dispatch('notify', title: __('admin_settings.users.notifications.impersonation_error_title'), message: __('admin_settings.users.notifications.impersonation_error_admin_message'), type: 'error');
        }
    }

    public function render(ListUsersUseCase $listUsersUseCase): View
    {
        $users = $listUsersUseCase->execute($this->userSearch, $this->perPage, RoleEnum::SCIENTIFIC_WORKER);

        // dump('UserManager RENDER: showCreateUserModal=' . ($this->showCreateUserModal ? 'true' : 'false') . ', showEditUserModal=' . ($this->showEditUserModal ? 'true' : 'false') . ', editingUserId=' . ($this->editingUserId ?? 'null'));

        return view('livewire.admin.settings.user-manager', [
            'users' => $users,
        ]);
    }
}
