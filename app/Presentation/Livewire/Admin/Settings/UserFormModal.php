<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\GetUserUseCase;
use App\Application\UseCases\User\UpdateUserUseCase;
use App\Domain\Dto\StoreUserDto;
use App\Domain\Dto\UpdateUserDto;
use App\Domain\Enums\RoleEnum;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Exception;

class UserFormModal extends Component
{
    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $role;

    public bool $isEditing = false;

    public bool $isVisible = false;

    public function mount(GetUserUseCase $getUserUseCase): void
    {
        $this->role = RoleEnum::SCIENTIFIC_WORKER->value;

        if ($this->isEditing && $this->userId && $this->userId !== 0) {
            $user = $getUserUseCase->execute($this->userId);

            if ($user && $user->role === RoleEnum::SCIENTIFIC_WORKER) {
                $this->name = $user->name;
                $this->email = $user->email;
            } else {
                $this->dispatch('notify', title: 'Error', message: 'User not found or cannot be edited.', type: 'error');
                $this->resetForm();
                $this->dispatch('closeUserFormModal');
            }
        } elseif (!$this->isEditing) {
            $this->resetForm();
        } else {
            $this->dispatch('notify', title: 'Error', message: 'Inconsistent state for user editing.', type: 'error');
            $this->resetForm();
            $this->dispatch('closeUserFormModal');
        }
    }

    public function saveUser(
        CreateUserUseCase $createUserUseCase,
        UpdateUserUseCase $updateUserUseCase,
    ): void {
        $roleEnum = RoleEnum::SCIENTIFIC_WORKER->value;

        if ($this->isEditing && $this->userId) {
            $updateData = [
                'id' => $this->userId,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $roleEnum,
            ];

            if (!empty($this->password)) {
                $updateData['password'] = $this->password;
                $updateData['password_confirmation'] = $this->password_confirmation;
            }
            $dto = UpdateUserDto::validateAndCreate($updateData);

            try {
                $updateUserUseCase->execute($dto);
            } catch (Exception $e) {
                $this->dispatch('notify', title: __('Error'), message: $e->getMessage(), type: 'error');

                return;
            }
        } else {
            $dto = StoreUserDto::validateAndCreate([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'role' => $roleEnum,
            ]);

            try {
                $createUserUseCase->execute($dto);
            } catch (Exception $e) {
                $this->dispatch('notify', title: __('Error'), message: $e->getMessage(), type: 'error');

                return;
            }
        }

        $this->dispatch('userSaved');
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->dispatch('closeUserFormModal');
    }

    private function resetForm(): void
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = RoleEnum::SCIENTIFIC_WORKER->value;
        $this->isEditing = false;
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.settings.user-form-modal');
    }
}
