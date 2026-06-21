<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('user_settings')
            ->where('se_volume', 30)
            ->where('taskkill_se_volume', 50)
            ->where('status_se_volume', 30)
            ->where('voice_volume', 50)
            ->update([
                'se_volume' => 50,
                'status_se_volume' => 50,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user_settings')
            ->where('se_volume', 50)
            ->where('taskkill_se_volume', 50)
            ->where('status_se_volume', 50)
            ->where('voice_volume', 50)
            ->update([
                'se_volume' => 30,
                'status_se_volume' => 30,
                'updated_at' => now(),
            ]);
    }
};
