<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Application\UseCases\User\ArchiveUserUseCase;
use App\Domain\Enums\RoleEnum;
use App\Infrastructure\Models\User;
use App\Presentation\Livewire\Admin\Settings\UserManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserRestoreModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_restore_failure_opens_error_modal_with_message(): void
    {
        $admin = User::factory()->administrator()->create();
        $this->actingAs($admin);

        $archivedUser = User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        app(ArchiveUserUseCase::class)->execute($archivedUser->id);

        User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        Livewire::test(UserManager::class, ['filterRole' => RoleEnum::SCIENTIFIC_WORKER])
            ->call('restoreUser', $archivedUser->id)
            ->assertSet('showRestoreErrorModal', true)
            ->assertSet('restoreErrorMessage', __('admin_settings.users.notifications.user_restore_email_taken_message'));
    }
}
