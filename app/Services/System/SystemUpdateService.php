<?php

namespace App\Services\System;

use App\Models\SystemUpdate;
use Symfony\Component\Process\Process;

class SystemUpdateService
{
    public function runUpdate(): SystemUpdate
    {
        $output = '';
        $status = 'successful';

        $version = trim(shell_exec('git rev-parse HEAD'));
        $commitTitle = trim(shell_exec('git log -1 --pretty=%s'));
        $commitDescription = trim(shell_exec('git log -1 --pretty=%b'));

        $update = SystemUpdate::create([
            'version' => $version,
            'commit_title' => $commitTitle,
            'commit_description' => $commitDescription,
            'update_log' => '',
            'status' => 'running',
        ]);

        $php = env('PHP_BINARY_PATH', '/usr/bin/php');
        $npm = env('NPM_BINARY_PATH', '/usr/bin/npm');
        $node = env('NODE_BINARY_PATH', '/usr/bin/node');
        $customEnv = [
            'PATH' => implode(':', [
                dirname($php),
                dirname($npm),
                dirname($node),
                '/bin',                // for local sh
                '/usr/bin',            // for server sh
                '/usr/local/bin',      // common extra tools
                getenv('PATH'),        // keep any existing path
            ]),
        ];
        $commands = [
            'git fetch origin',
            'git reset --hard origin/main',
            'git clean -fd',
            "$php artisan migrate",
            "$php artisan optimize:clear",
            "$php artisan queue:restart",
            "$npm install",
            "$npm run build",
        ];

        foreach ($commands as $command) {
            $output .= "Running: $command\n";
            try {
                $process = Process::fromShellCommandline($command, base_path(), $customEnv, null, 300);
                $process->run();

                $output .= $process->getOutput() . "\n";
                if (!$process->isSuccessful()) {
                    $output .= "Error: " . $process->getErrorOutput() . "\n";
                    $status = 'failed';
                    break;
                }
            } catch (\Throwable $e) {
                $output .= "Exception: " . $e->getMessage() . "\n";
                $status = 'failed';
                break;
            }
        }

        $update->update([
            'update_log' => $output,
            'status' => $status,
        ]);

        return $update;
    }

    public function checkForUpdates(): ?array
    {
        $currentVersion = trim(shell_exec('git rev-parse HEAD'));
        $latestVersion = trim(shell_exec('git ls-remote origin -h refs/heads/main | cut -f1'));

        if ($currentVersion !== $latestVersion) {
            return [
                'version' => $latestVersion,
                'title' => trim(shell_exec("git log {$latestVersion} -1 --pretty=%s")),
                'description' => trim(shell_exec("git log {$latestVersion} -1 --pretty=%b")),
            ];
        }

        return null;
    }
}
