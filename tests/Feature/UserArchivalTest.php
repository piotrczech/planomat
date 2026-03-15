<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Application\UseCases\User\ArchiveUserUseCase;
use App\Application\UseCases\User\RestoreUserUseCase;
use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class UserArchivalTest extends TestCase
{
    use RefreshDatabase;

    public function test_archiving_user_adds_incremental_archive_email_suffix_and_deactivates_account(): void
    {
        $archiveUserUseCase = app(ArchiveUserUseCase::class);

        $firstUser = User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        $this->assertTrue($archiveUserUseCase->execute($firstUser->id));

        $firstUserArchived = User::withTrashed()->findOrFail($firstUser->id);
        $this->assertSame('piotr+archiwizacja1@pwr.edu.pl', $firstUserArchived->email);
        $this->assertFalse($firstUserArchived->is_active);
        $this->assertNotNull($firstUserArchived->deleted_at);

        $secondUser = User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        $this->assertTrue($archiveUserUseCase->execute($secondUser->id));

        $secondUserArchived = User::withTrashed()->findOrFail($secondUser->id);
        $this->assertSame('piotr+archiwizacja2@pwr.edu.pl', $secondUserArchived->email);
        $this->assertFalse($secondUserArchived->is_active);
        $this->assertNotNull($secondUserArchived->deleted_at);
    }

    public function test_restore_within_24_hours_restores_base_email_and_reactivates_account(): void
    {
        $archiveUserUseCase = app(ArchiveUserUseCase::class);
        $restoreUserUseCase = app(RestoreUserUseCase::class);

        $user = User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        $this->assertTrue($archiveUserUseCase->execute($user->id));
        $this->assertTrue($restoreUserUseCase->execute($user->id));

        $restoredUser = User::findOrFail($user->id);
        $this->assertSame('piotr@pwr.edu.pl', $restoredUser->email);
        $this->assertTrue($restoredUser->is_active);
        $this->assertNull($restoredUser->deleted_at);
    }

    public function test_restore_is_rejected_after_24_hours(): void
    {
        $archiveUserUseCase = app(ArchiveUserUseCase::class);
        $restoreUserUseCase = app(RestoreUserUseCase::class);

        $user = User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
        ]);

        $archiveUserUseCase->execute($user->id);
        $this->travel(25)->hours();

        try {
            $restoreUserUseCase->execute($user->id);
            $this->fail('Expected RuntimeException was not thrown.');
        } catch (RuntimeException) {
            $this->assertNotNull(User::withTrashed()->findOrFail($user->id)->deleted_at);
        }
    }

    public function test_restore_is_rejected_when_base_email_is_taken(): void
    {
        $archiveUserUseCase = app(ArchiveUserUseCase::class);
        $restoreUserUseCase = app(RestoreUserUseCase::class);

        $archivedUser = User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        $archiveUserUseCase->execute($archivedUser->id);

        User::factory()->scientificWorker()->create([
            'email' => 'piotr@pwr.edu.pl',
            'is_active' => true,
        ]);

        try {
            $restoreUserUseCase->execute($archivedUser->id);
            $this->fail('Expected RuntimeException was not thrown.');
        } catch (RuntimeException) {
            $restoredAttemptUser = User::withTrashed()->findOrFail($archivedUser->id);
            $this->assertNotNull($restoredAttemptUser->deleted_at);
            $this->assertSame('piotr+archiwizacja1@pwr.edu.pl', $restoredAttemptUser->email);
        }
    }
}
