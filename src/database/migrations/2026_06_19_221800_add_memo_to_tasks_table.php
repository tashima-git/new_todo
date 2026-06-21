<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tasks', 'memo')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->text('memo')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('tasks', 'memo')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('memo');
        });
    }
};
