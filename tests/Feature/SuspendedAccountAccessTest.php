<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Application\Exceptions\SuspendedAccountException;
use App\Application\UseCases\Auth\LoginViaUsosUseCase;
use App\Domain\Dto\ExternalAuthUserDto;
use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SuspendedAccountAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspended_user_is_logged_out_by_active_user_middleware(): void
    {
        $suspendedUser = User::factory()->scientificWorker()->create([
            'is_active' => false,
        ]);

        $this->actingAs($suspendedUser)
            ->get(route('dashboard'))
            ->assertRedirect(route('account.suspended'));

        $this->assertGuest();
    }

    public function test_local_login_redirects_suspended_user_to_suspended_account_view(): void
    {
        $suspendedUser = User::factory()->scientificWorker()->create([
            'email' => 'suspended@pwr.edu.pl',
            'password' => Hash::make('secret123'),
            'is_active' => false,
        ]);

        Livewire::test('auth.login')
            ->set('email', $suspendedUser->email)
            ->set('password', 'secret123')
            ->call('login')
            ->assertRedirect(route('account.suspended', absolute: false));

        $this->assertGuest();
    }

    public function test_usos_login_throws_dedicated_exception_for_suspended_account(): void
    {
        $suspendedUser = User::factory()->scientificWorker()->create([
            'email' => 'suspended-usos@pwr.edu.pl',
            'is_active' => false,
        ]);

        $dto = new ExternalAuthUserDto(
            id: 'usos-123',
            email: $suspendedUser->email,
            firstName: 'Piotr',
            lastName: 'Nowak',
            academicTitle: '',
        );

        $this->expectException(SuspendedAccountException::class);

        app(LoginViaUsosUseCase::class)->execute($dto);
    }
}
