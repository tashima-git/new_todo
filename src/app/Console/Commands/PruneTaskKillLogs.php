<?php

namespace App\Console\Commands;

use App\Models\TaskKillLog;
use Illuminate\Console\Command;

class PruneTaskKillLogs extends Command
{
    protected $signature = 'taskkill:prune-mob-logs {--days=30 : Number of days to keep mob task logs}';
    protected $description = 'Delete old mob task kill logs to keep task_kill_logs table size under control.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $deleted = TaskKillLog::pruneOldMobLogs($days);

        $this->info("Deleted {$deleted} mob task logs older than {$days} days.");

        return Command::SUCCESS;
    }
}
