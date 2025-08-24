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
use App\Application\UseCases\AcademicTitle\ListAcademicTitlesUseCase;

class UserFormModal extends Component
{
    public ?int $userId = null;

    public string $academic_title = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $role;

    public bool $isEditing = false;

    public bool $isVisible = false;

    public RoleEnum $userRole;

    public function mount(RoleEnum $userRole, GetUserUseCase $getUserUseCase): void
    {
        $this->userRole = $userRole;
        $this->role = $userRole->value;

        if ($this->isEditing && $this->userId && $this->userId !== 0) {
            $user = $getUserUseCase->execute($this->userId);

            if ($user && $user->role === $userRole) {
                $this->academic_title = $user->academic_title ?? '';
                $this->first_name = $user->first_name ?? '';
                $this->last_name = $user->last_name ?? '';
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
        $roleEnum = $this->userRole->value;

        if ($this->isEditing && $this->userId) {
            $updateData = [
                'id' => $this->userId,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'academic_title' => $this->academic_title ?: null,
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
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'academic_title' => $this->academic_title ?: null,
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
        $this->first_name = '';
        $this->last_name = '';
        $this->academic_title = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = $this->userRole->value;
        $this->isEditing = false;
        $this->resetErrorBag();
    }

    public function render(ListAcademicTitlesUseCase $listAcademicTitlesUseCase): View
    {
        $academicTitles = $listAcademicTitlesUseCase->execute();

        return view('livewire.admin.settings.user-form-modal', compact('academicTitles'));
    }
}
