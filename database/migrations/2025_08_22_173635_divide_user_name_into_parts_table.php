<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('academic_title')->nullable()->after('id');
            $table->foreign('academic_title')->references('title')->on('academic_titles')->nullOnDelete();

            $table->string('first_name')->nullable()->after('academic_title');
            $table->string('last_name')->nullable()->after('first_name');

            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('name')->nullable()->after('id');

            if (Schema::hasColumn('users', 'academic_title')) {
                $table->dropForeign(['academic_title']);
                $table->dropColumn('academic_title');
            }

            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }

            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
        });
    }
};
