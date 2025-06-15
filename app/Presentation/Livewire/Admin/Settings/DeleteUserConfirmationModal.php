<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\User\GetUserUseCase; // To potentially show user name
use App\Infrastructure\Models\User;
use Livewire\Component;
use Illuminate\Contracts\View\View;

// Dodano import dla spójności

class DeleteUserConfirmationModal extends Component
{
    public ?int $userId = null;

    public ?User $userToDelete = null;

    public bool $isVisible = false;

    public function mount(?int $userId, bool $isVisible, GetUserUseCase $getUserUseCase): void
    {
        $this->isVisible = $isVisible;

        if ($userId) {
            $this->loadUser($userId, $getUserUseCase);
        }
    }

    // This method can be called by parent to set the user ID and load data
    public function loadUser(int $userId, GetUserUseCase $getUserUseCase): void
    {
        $this->userId = $userId;
        $this->userToDelete = $getUserUseCase->execute($userId);
    }

    public function confirmDeletion(): void
    {
        if ($this->userId) {
            $this->dispatch('deleteUserConfirmed', userId: $this->userId);
            // The parent (UserManager) will handle closing this modal via its own state variable
            // or by emitting 'closeDeleteConfirmationModal' event if needed.
        }
        // Fallback if dispatch to parent doesn't close it:
        // $this->dispatch('closeDeleteConfirmationModal');
    }

    public function cancel(): void
    {
        $this->dispatch('closeDeleteConfirmationModal');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.delete-user-confirmation-modal');
    }
}
