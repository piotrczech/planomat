<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $activeSemesterExists = DB::table('semesters')->where('is_active', true)->exists();

        if (!$activeSemesterExists) {
            $latestSemester = DB::table('semesters')
                ->orderBy('start_year', 'desc')
                ->orderBy('season', 'desc')
                ->first();

            if ($latestSemester) {
                DB::table('semesters')
                    ->where('id', $latestSemester->id)
                    ->update(['is_active' => true]);
            }
        }
    }

    public function down(): void
    {
        DB::table('semesters')->update(['is_active' => false]);
    }
};
