<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => 'free'],
            [
                'max_daily' => 10,
                'max_weekly' => 3,
                'max_monthly' => 1,
                'max_task_changes' => 1,
            ]
        );

        Plan::firstOrCreate(
            ['name' => 'paid'],
            [
                'max_daily' => 100,
                'max_weekly' => 50,
                'max_monthly' => 10,
                'max_task_changes' => null,
            ]
        );
    }
}
