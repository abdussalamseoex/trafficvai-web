<?php

namespace App\Services;

use App\Models\UpdateLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class UpdateService
{
    public function checkForUpdates()
    {
        try {
            if (!$this->isGitAvailable()) {
                return [
                    'update_available' => false,
                    'error' => 'Git is not installed or not in the PHP shell_exec path. Please check your server configuration.'
                ];
            }

            // First, fetch the latest from the remote
            $this->executeCommand('git fetch');

            // Compare local branch with remote
            $local = $this->executeCommand('git rev-parse HEAD');
            $remote = $this->executeCommand('git rev-parse @{u}');

            // Check if commands returned valid hashes (40 chars hex)
            if (!preg_match('/^[a-f0-9]{40}$/', $local) || !preg_match('/^[a-f0-9]{40}$/', $remote)) {
                 return [
                    'update_available' => false,
                    'error' => 'Git repository is not initialized or tracking is broken. Output: ' . $remote
                ];
            }

            $updateAvailable = ($local !== $remote);
            $changes = $updateAvailable ? $this->executeCommand('git log HEAD..@{u} --oneline') : '';

            return [
                'update_available' => $updateAvailable,
                'local_version' => substr($local, 0, 7),
                'remote_version' => substr($remote, 0, 7),
                'changes' => $changes,
            ];
        } catch (\Exception $e) {
            Log::error('UpdateService Check Error: ' . $e->getMessage());
            return ['update_available' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check if git is available in the shell path.
     * 
     * @return bool
     */
    public function isGitAvailable()
    {
        $output = $this->executeCommand('git --version');
        return str_contains(strtolower($output), 'git version');
    }

    /**
     * Get the current system status for the UI.
     * 
     * @return array
     */
    public function getSystemStatus()
    {
        if (!$this->isGitAvailable()) {
            return ['error' => 'Git not found'];
        }

        $local = substr($this->executeCommand('git rev-parse HEAD'), 0, 7);
        $branch = $this->executeCommand('git rev-parse --abbrev-ref HEAD');

        return [
            'version' => $local,
            'branch' => $branch,
            'git' => true
        ];
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
            // Get current branch name
            $currentBranch = $this->executeCommand('git rev-parse --abbrev-ref HEAD') ?: 'main';

            // 1. Git Pull
            $output .= "--- Pulling latest code for branch: {$currentBranch} ---\n";
            $output .= $this->executeCommand("git fetch origin {$currentBranch}");
            $output .= $this->executeCommand("git reset --hard origin/{$currentBranch}");

            // 2. Composer Install (if composer.json changed)
            // Note: In cPanel environment, shell_exec might not have composer in PATH.
            // We'll skip it unless specifically requested or if we can find the path.
            // For now, let's assume the user handles major dependency changes or we use a common path.
            /*
            $output .= "\n--- Running Composer Install ---\n";
            $output .= $this->executeCommand('composer install --no-interaction --prefer-dist --optimize-autoloader');
            */

            // 2. Composer Install
            $output .= "\n--- Running Composer Install ---\n";
            $composerPaths = ['composer', '/usr/bin/composer', '/usr/local/bin/composer', '/opt/cpanel/composer/bin/composer'];
            $composerCmd = null;
            foreach ($composerPaths as $path) {
                $test = shell_exec("which {$path} 2>/dev/null");
                if (!empty(trim($test ?? ''))) {
                    $composerCmd = $path;
                    break;
                }
            }
            if ($composerCmd) {
                // Set HOME for cPanel environments where it may not be defined
                $homeDir = trim(shell_exec('echo $HOME 2>/dev/null') ?? '') ?: '/tmp';
                $output .= $this->executeCommand("HOME={$homeDir} COMPOSER_HOME={$homeDir}/.composer {$composerCmd} install --no-interaction --no-dev --optimize-autoloader");
            } else {
                $output .= "(Composer not found in PATH — skipping. Run fix-composer.php manually if needed.)\n";
            }

            // 3. Migrate

            $output .= "\n--- Running Migrations ---\n";
            Artisan::call('migrate', ['--force' => true]);
            $output .= Artisan::output();

            $output .= "\n--- Seeding FAQs ---\n";
            Artisan::call('db:seed', ['--class' => 'SiteFaqSeeder', '--force' => true]);
            $output .= Artisan::output();

            $output .= "\n--- Seeding Service Pages ---\n";
            Artisan::call('db:seed', ['--class' => 'ServicePageSeeder', '--force' => true]);
            $output .= Artisan::output();

            // 4. Clear all caches (do NOT re-cache routes as this causes issues when new routes are added)
            $output .= "\n--- Refreshing Cache ---\n";
            Artisan::call('optimize:clear');
            $output .= Artisan::output();

            Artisan::call('route:clear');
            $output .= Artisan::output();

            Artisan::call('view:clear');
            $output .= Artisan::output();

            $output .= "\n--- Restarting Queue Workers ---\n";
            Artisan::call('queue:restart');
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

    /**
     * Sync system database and cache without pulling new code.
     * 
     * @return array
     */
    public function syncSystem()
    {
        $log = UpdateLog::create([
            'status' => 'pending',
            'executed_at' => now(),
        ]);

        $output = "--- Manual System Sync Started ---\n";
        
        try {
            $output .= "\n--- Running Migrations ---\n";
            Artisan::call('migrate', ['--force' => true]);
            $output .= Artisan::output();

            $output .= "\n--- Seeding Service Pages ---\n";
            Artisan::call('db:seed', ['--class' => 'ServicePageSeeder', '--force' => true]);
            $output .= Artisan::output();

            $output .= "\n--- Refreshing Cache ---\n";
            Artisan::call('optimize:clear');
            $output .= Artisan::output();

            $output .= "\n--- Restarting Queue Workers ---\n";
            Artisan::call('queue:restart');
            $output .= Artisan::output();

            $log->update([
                'status' => 'success',
                'output' => $output,
                'version' => substr($this->executeCommand('git rev-parse HEAD'), 0, 7),
            ]);

            return ['success' => true, 'output' => $output];

        } catch (\Exception $e) {
            $output .= "\n--- ERROR ---\n" . $e->getMessage();
            $log->update(['status' => 'error', 'output' => $output]);
            return ['success' => false, 'error' => $e->getMessage(), 'output' => $output];
        }
    }
}
