<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('se_volume')->default(50);
            $table->unsignedTinyInteger('taskkill_se_volume')->default(50);
            $table->unsignedTinyInteger('status_se_volume')->default(50);
            $table->string('voice_type', 32)->default('none');
            $table->unsignedTinyInteger('voice_volume')->default(50);
            $table->string('default_task_view', 32)->default('tree');
            $table->boolean('confirm_important_actions')->default(true);
            $table->boolean('deadline_notification_enabled')->default(false);
            $table->string('deadline_notification_timing', 32)->default('same_day');
            $table->unsignedTinyInteger('tasks_per_page')->default(10);
            $table->unsignedTinyInteger('auto_strategy_on_create')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
