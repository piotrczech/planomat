<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Application\UseCases\User\SetUserActiveUseCase;
use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_deactivation_revokes_all_sessions_for_target_user(): void
    {
        $targetUser = User::factory()->scientificWorker()->create(['is_active' => true]);
        $otherUser = User::factory()->scientificWorker()->create(['is_active' => true]);

        $sessionTable = (string) config('session.table', 'sessions');

        DB::table($sessionTable)->insert([
            [
                'id' => 'target-session-1',
                'user_id' => $targetUser->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'payload' => 'payload-1',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'target-session-2',
                'user_id' => $targetUser->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'payload' => 'payload-2',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'other-session-1',
                'user_id' => $otherUser->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'payload' => 'payload-3',
                'last_activity' => now()->timestamp,
            ],
        ]);

        $updated = app(SetUserActiveUseCase::class)->execute($targetUser->id, false);

        $this->assertTrue($updated);
        $this->assertFalse($targetUser->fresh()->is_active);
        $this->assertDatabaseMissing($sessionTable, ['id' => 'target-session-1']);
        $this->assertDatabaseMissing($sessionTable, ['id' => 'target-session-2']);
        $this->assertDatabaseHas($sessionTable, ['id' => 'other-session-1']);
    }
}
