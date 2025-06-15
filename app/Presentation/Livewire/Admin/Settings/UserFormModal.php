<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin\Settings;

use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\GetUserUseCase;
use App\Application\UseCases\User\UpdateUserUseCase;
use App\Domain\Dto\StoreUserDto;
use App\Domain\Dto\UpdateUserDto;
use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User; // Potrzebne do rzutowania pól formularza na DTO
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

    public int $role; // To pole będzie zawsze RoleEnum::SCIENTIFIC_WORKER->value

    public bool $isEditing = false;

    public bool $isVisible = false; // Ta właściwość będzie synchronizowana z rodzicem przez @entangle
    // public string $uniqueKey = ''; // Do debugowania klucza

    public function mount(GetUserUseCase $getUserUseCase): void
    {
        // dump('UserFormModal MOUNT START - Component ID: ' . $this->getId() . ' - Props: isEditing=' . var_export($this->isEditing, true) . ', userId=' . var_export($this->userId, true) . ', isVisible=' . var_export($this->isVisible, true));

        $this->role = RoleEnum::SCIENTIFIC_WORKER->value;

        if ($this->isEditing && $this->userId && $this->userId !== 0) {
            // dump('UserFormModal MOUNT: Path A - Attempting Edit Load. Component ID: ' . $this->getId());
            $user = $getUserUseCase->execute($this->userId);

            if ($user && $user->role === RoleEnum::SCIENTIFIC_WORKER) {
                $this->name = $user->name;
                $this->email = $user->email;
                // dump('UserFormModal MOUNT: Path A - Edit Load SUCCESS. Component ID: ' . $this->getId() . ', Name: ' . $this->name);
            } else {
                // dump('UserFormModal MOUNT: Path A - Edit Load FAILED (user not found/wrong role). Component ID: ' . $this->getId());
                $this->dispatch('notify', title: 'Error', message: 'User not found or cannot be edited.', type: 'error');
                $this->resetForm();
                $this->dispatch('closeUserFormModal');
            }
        } elseif (!$this->isEditing) {
            // dump('UserFormModal MOUNT: Path B - Create Mode (or edit modal being reset due to !isEditing). Component ID: ' . $this->getId());
            $this->resetForm();
        } else { // This case: $this->isEditing is true, but $this->userId is null or 0
            // dump('UserFormModal MOUNT: Path C - Inconsistent Edit State (isEditing=true, but no valid userId). Component ID: ' . $this->getId());
            $this->dispatch('notify', title: 'Error', message: 'Inconsistent state for user editing.', type: 'error');
            $this->resetForm();
            $this->dispatch('closeUserFormModal');
        }
        // dump('UserFormModal MOUNT END - Component ID: ' . $this->getId() . ' - Final State: isEditing=' . var_export($this->isEditing, true) . ', userId=' . var_export($this->userId, true) . ', Name: ' . $this->name);
    }

    public function saveUser(
        CreateUserUseCase $createUserUseCase,
        UpdateUserUseCase $updateUserUseCase,
    ): void {
        // dump('UserFormModal SAVEUSER START - Component ID: ' . $this->getId() . ' - Current State: isEditing=' . var_export($this->isEditing, true) . ', userId=' . var_export($this->userId, true) . ', Name: ' . $this->name);

        $roleEnum = RoleEnum::SCIENTIFIC_WORKER->value;

        if ($this->isEditing && $this->userId) {
            // dump('UserFormModal saveUser: Entering EDIT block. Component ID: ' . $this->getId());

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

            // dump('UserFormModal saveUser: UpdateUserDto ready', $dto->all());
            try {
                $updateUserUseCase->execute($dto);
                // dump('UserFormModal saveUser: UpdateUserUseCase executed for EDIT');
            } catch (Exception $e) {
                // dump('UserFormModal saveUser: EXCEPTION during UpdateUserUseCase', $e->getMessage());
                $this->dispatch('notify', title: __('Error'), message: $e->getMessage(), type: 'error');

                return; // Przerwij, jeśli był błąd
            }
        } else {
            // dump('UserFormModal saveUser: Entering CREATE block. Component ID: ' . $this->getId());
            $dto = StoreUserDto::validateAndCreate([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'role' => $roleEnum,
            ]);

            // dump('UserFormModal saveUser: StoreUserDto ready', $dto->all());
            try {
                $createUserUseCase->execute($dto);
                // dump('UserFormModal saveUser: CreateUserUseCase executed for CREATE');
            } catch (Exception $e) {
                // dump('UserFormModal saveUser: EXCEPTION during CreateUserUseCase', $e->getMessage());
                $this->dispatch('notify', title: __('Error'), message: $e->getMessage(), type: 'error');

                return; // Przerwij
            }
        }

        // dump('UserFormModal saveUser: Dispatching userSaved event. Component ID: ' . $this->getId());
        $this->dispatch('userSaved');
    }

    public function closeModal(): void
    {
        $this->resetForm();
        // $this->isVisible = false; // Nie ustawiamy tutaj, rodzic to zrobi przez prop
        $this->dispatch('closeUserFormModal'); // Emitujemy zdarzenie do rodzica (UserManager)
    }

    private function resetForm(): void
    {
        // dump('UserFormModal resetForm CALLED - Component ID: ' . $this->getId() . ' - Resetting state. Current isEditing before reset: ' . var_export($this->isEditing, true));
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = RoleEnum::SCIENTIFIC_WORKER->value;
        $this->isEditing = false; // Ważne: resetForm zawsze ustawia isEditing na false
        $this->resetErrorBag();
    }

    public function render(): View
    {
        // dump('UserFormModal RENDER: isVisible=' . ($this->isVisible ? 'true' : 'false') . ', userId=' . ($this->userId ?? 'null'));
        return view('livewire.admin.settings.user-form-modal');
    }
}
