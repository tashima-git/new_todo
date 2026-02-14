<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_kill_logs', function (Blueprint $table) {
            $table->id();

            // タスク情報（軽量保存）
            $table->string('task_title');
            $table->dateTime('task_created_at');
            $table->dateTime('task_completed_at');

            // 所有者
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 種別
            $table->enum('boss_type', ['mob', 'mid', 'boss']);

            // 獲得ステ
            $table->integer('gained_patience')->default(0);
            $table->integer('gained_speed')->default(0);
            $table->integer('gained_focus')->default(0);
            $table->integer('gained_accuracy')->default(0);
            $table->integer('gained_life')->default(0);
            $table->integer('gained_strategy')->default(0);

            $table->timestamps();

            // インデックス（統計で使う）
            $table->index(['user_id', 'boss_type']);
            $table->index(['user_id', 'task_completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_kill_logs');
    }
};
