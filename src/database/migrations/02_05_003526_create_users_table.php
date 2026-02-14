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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('plan_id');
            $table->integer('total_patience')->default(0);
            $table->integer('total_speed')->default(0);
            $table->integer('total_focus')->default(0);
            $table->integer('total_accuracy')->default(0);
            $table->integer('total_life')->default(0);
            $table->integer('total_strategy')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
