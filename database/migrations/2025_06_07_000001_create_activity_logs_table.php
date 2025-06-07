<?php

declare(strict_types=1);

use App\Enums\ActivityLogActionEnum;
use App\Enums\ActivityLogModuleEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->enum('module', ActivityLogModuleEnum::values());
            $table->enum('action', ActivityLogActionEnum::values());
            $table->timestamp('created_at')->default(now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
