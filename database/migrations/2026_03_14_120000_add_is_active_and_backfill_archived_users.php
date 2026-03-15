<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('role');
            $table->index('is_active');
        });

        $archivedUsers = DB::table('users')
            ->whereNotNull('deleted_at')
            ->orderBy('id')
            ->get(['id', 'email']);

        foreach ($archivedUsers as $archivedUser) {
            $baseEmail = $this->stripArchiveSuffix((string) $archivedUser->email);
            $archivedEmail = $this->resolveArchivedEmailCandidate((int) $archivedUser->id, $baseEmail);

            DB::table('users')
                ->where('id', $archivedUser->id)
                ->update([
                    'email' => $archivedEmail,
                    'usos_id' => null,
                    'is_active' => false,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['is_active']);
            $table->dropColumn('is_active');
        });
    }

    private function resolveArchivedEmailCandidate(int $userId, string $baseEmail): string
    {
        [$localPart, $domainPart] = $this->splitEmail($baseEmail);

        if ($domainPart === '') {
            return $baseEmail;
        }

        $archiveIndex = 1;

        do {
            $candidate = sprintf('%s+archiwizacja%d@%s', $localPart, $archiveIndex, $domainPart);

            $exists = DB::table('users')
                ->where('email', $candidate)
                ->where('id', '!=', $userId)
                ->exists();

            $archiveIndex++;
        } while ($exists);

        return $candidate;
    }

    private function stripArchiveSuffix(string $email): string
    {
        [$localPart, $domainPart] = $this->splitEmail($email);
        $baseLocalPart = preg_replace('/\+archiwizacja\d+$/', '', $localPart) ?? $localPart;

        return sprintf('%s@%s', $baseLocalPart, $domainPart);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitEmail(string $email): array
    {
        $parts = explode('@', $email, 2);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return [$email, ''];
        }

        return [$parts[0], $parts[1]];
    }
};
