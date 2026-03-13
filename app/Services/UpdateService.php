<?php

namespace App\Services;

use App\Models\UpdateLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class UpdateService
{
    /**
     * Check if there are updates available on GitHub.
     * 
     * @return array
     */
    public function checkForUpdates()
    {
        try {
            // First, fetch the latest from the remote
            $this->executeCommand('git fetch');

            // Compare local branch with remote
            $local = $this->executeCommand('git rev-parse HEAD');
            $remote = $this->executeCommand('git rev-parse @{u}');

            if ($local !== $remote) {
                // Get the changes/commit messages
                $changes = $this->executeCommand('git log HEAD..@{u} --oneline');
                return [
                    'update_available' => true,
                    'remote_version' => substr($remote, 0, 7),
                    'changes' => $changes,
                ];
            }

            return ['update_available' => false];
        } catch (\Exception $e) {
            Log::error('UpdateService Check Error: ' . $e->getMessage());
            return ['update_available' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Apply the latest updates.
     * 
     * @return array
     */
    public function applyUpdate()
    {
        $log = UpdateLog::create([
            'status' => 'pending',
            'executed_at' => now(),
        ]);

        $output = "";
        $success = true;

        try {
            // 1. Git Pull
            $output .= "--- Pulling latest code ---\n";
            $output .= $this->executeCommand('git fetch origin main');
            $output .= $this->executeCommand('git reset --hard origin/main');

            // 2. Composer Install (if composer.json changed)
            // Note: In cPanel environment, shell_exec might not have composer in PATH.
            // We'll skip it unless specifically requested or if we can find the path.
            // For now, let's assume the user handles major dependency changes or we use a common path.
            /*
            $output .= "\n--- Running Composer Install ---\n";
            $output .= $this->executeCommand('composer install --no-interaction --prefer-dist --optimize-autoloader');
            */

            // 3. Migrate
            $output .= "\n--- Running Migrations ---\n";
            Artisan::call('migrate', ['--force' => true]);
            $output .= Artisan::output();

            // 4. Optimize Clear & Cache
            $output .= "\n--- Refreshing Cache ---\n";
            Artisan::call('optimize:clear');
            $output .= Artisan::output();
            
            Artisan::call('optimize');
            $output .= Artisan::output();

            $log->update([
                'status' => 'success',
                'output' => $output,
                'version' => substr($this->executeCommand('git rev-parse HEAD'), 0, 7),
            ]);

            return ['success' => true, 'output' => $output];

        } catch (\Exception $e) {
            $success = false;
            $output .= "\n--- ERROR ---\n" . $e->getMessage();
            
            $log->update([
                'status' => 'error',
                'output' => $output,
            ]);

            Log::error('UpdateService Apply Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage(), 'output' => $output];
        }
    }

    /**
     * Execute a shell command and return the output.
     * 
     * @param string $command
     * @return string
     */
    private function executeCommand($command)
    {
        $output = shell_exec($command . " 2>&1");
        return trim($output);
    }
}
