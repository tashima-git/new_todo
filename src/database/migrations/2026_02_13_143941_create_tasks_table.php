<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // 所有者
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // タスク名
            $table->string('title');

            // カテゴリ（仕事/プライベート）
            $table->enum('category', ['work', 'private']);

            // 期限（任意）
            $table->date('due_date')->nullable();

            // 重要度（1〜5）
            $table->tinyInteger('importance')->default(3);

            // 緊急（急ぎ）
            $table->boolean('is_urgent')->default(false);

            // 状態
            $table->enum('status', ['pending', 'stocked', 'killed'])->default('pending');

            // ステータス割り振り（6項目）
            $table->integer('stat_patience')->default(0);
            $table->integer('stat_speed')->default(0);
            $table->integer('stat_focus')->default(0);
            $table->integer('stat_accuracy')->default(0);
            $table->integer('stat_life')->default(0);
            $table->integer('stat_strategy')->default(0);

            // ボスツリー（親タスク）
            $table->foreignId('parent_task_id')
                ->nullable()
                ->constrained('tasks')
                ->nullOnDelete();

            // 完了した時刻（討伐待ちに入った時刻）
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // インデックス（MVPでも効く）
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
            $table->index(['parent_task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
