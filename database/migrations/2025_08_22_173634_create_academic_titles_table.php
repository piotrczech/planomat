<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_titles', function (Blueprint $table): void {
            $table->string('title')->primary();
            $table->string('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_titles');
    }
};
