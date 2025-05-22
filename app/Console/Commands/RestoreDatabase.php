<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RestoreDatabase extends Command
{

    /**
     * The name and signature of the console command.
     * If you omit {file}, it will auto-detect the latest backup in storage/backups.
     *
     * @var string
     */
    protected $signature = 'app:restore-database
                            {file? : Path to the SQL dump to restore.}';
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileArg = $this->argument('file');
        $dumpFile = $fileArg
            ? base_path($fileArg)
            : $this->findLatestDump();

        if (! $dumpFile || ! file_exists($dumpFile)) {
            $this->error("No dump file found to restore. Tried: {$dumpFile}");
            return self::FAILURE;
        }

        $this->info("Restoring database from: {$dumpFile}");

        $conn = config('database.default');
        $conf = config("database.connections.{$conn}", []);
        $host = $conf['host']     ?? '127.0.0.1';
        $db   = $conf['database'] ?? '';
        $user = $conf['username'] ?? '';
        $pass = $conf['password'] ?? '';

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

        $cmdParts = array_merge(
            ['mysql'],
            $flags,
            [escapeshellarg($db), '<', escapeshellarg($dumpFile)]
        );
        $full = implode(' ', $cmdParts);

        $this->line("Running: {$full}");
        $process = Process::fromShellCommandline($full, base_path());
        $process->run(function ($type, $output) {
            $this->output->write($output);
        });

        if (! $process->isSuccessful()) {
            $this->error("Restore failed:\n" . $process->getErrorOutput());
            return self::FAILURE;
        }

        $this->info("✔ Database restored successfully.");
        return self::SUCCESS;
    }

    /**
     * Scan storage/backups for the most recent .sql file.
     *
     * @return string|null
     */
    protected function findLatestDump(): ?string
    {
        $dir = storage_path('backups');
        if (! is_dir($dir)) {
            return null;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
        if (empty($files)) {
            return null;
        }

        // Sort by modification time, descending
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return $files[0];
    }
}
