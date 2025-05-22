<?php

namespace App\Console\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';
    protected $description = 'Dump the database (excluding migrations, jobs, failed_jobs) to storage/backups';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting database backup…');

        $conn   = config('database.default');
        $conf   = config("database.connections.{$conn}");
        $host   = $conf['host']     ?? '127.0.0.1';
        $db     = $conf['database'] ?? '';
        $user   = $conf['username'] ?? '';
        $pass   = $conf['password'] ?? '';

        if (empty($db)) {
            $this->error('No database name configured!');
            return self::FAILURE;
        }

        $flags = [];
        $flags[] = '-h' . escapeshellarg($host);
        if ($user !== '') {
            $flags[] = '-u' . escapeshellarg($user);
        }
        if ($pass !== '') {
            $flags[] = '-p' . escapeshellarg($pass);
        }

        $ts      = now()->format('Ymd_His');
        $dumpDir = storage_path('backups');
        if (! is_dir($dumpDir) && ! mkdir($dumpDir, 0755, true) && ! is_dir($dumpDir)) {
            throw new RuntimeException("Could not create {$dumpDir}");
        }
        $dumpFile = "{$dumpDir}/db-backup-{$ts}.sql";

        $ignore = ['migrations', 'jobs', 'failed_jobs'];
        $ignoreFlags = array_map(
            fn($tbl) => "--ignore-table={$db}.{$tbl}",
            $ignore
        );

        $cmd = array_merge(
            ['mysqldump'],
            $flags,
            $ignoreFlags,
            [escapeshellarg($db), '>', escapeshellarg($dumpFile)]
        );
        $full = implode(' ', $cmd);

        $this->line("Running: {$full}");
        $process = Process::fromShellCommandline($full, base_path());
        $process->run(fn($type, $out) => $this->output->write($out));

        if (! $process->isSuccessful()) {
            $this->error("Backup failed:\n" . $process->getErrorOutput());
            return self::FAILURE;
        }

        $this->info("✔ Backup saved to {$dumpFile}");
        return self::SUCCESS;
    }
}
