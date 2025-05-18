<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\System\SystemUpdateService;

class RunSystemUpdate extends Command
{
    protected $signature = 'system:update';
    protected $description = 'Run the system update commands and log the result';

    public function handle()
    {
        $update = app(SystemUpdateService::class)->runUpdate();

        $this->info("System update {$update->status}: {$update->version}");
    }
}
