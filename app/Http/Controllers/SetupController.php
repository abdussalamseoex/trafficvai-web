<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PDO;
use Exception;

class SetupController extends Controller
{
    public function index()
    {
        // Security: Check if setup is already locked
        if (File::exists(storage_path('app/setup.lock'))) {
            return redirect()->route('login')->with('error', 'Setup is already completed and locked for security.');
        }

        return view('setup.database');
    }

    public function setup(Request $request)
    {
        if (File::exists(storage_path('app/setup.lock'))) {
            return response()->json(['success' => false, 'message' => 'Setup is locked.'], 403);
        }

        $request->validate([
            'db_host' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_pass' => 'nullable',
        ]);

        try {
            // 1. Test Connection
            $dsn = "mysql:host={$request->db_host};dbname={$request->db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            new PDO($dsn, $request->db_user, $request->db_pass, $options);

            // 2. Update .env file
            $this->updateEnv([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_user,
                'DB_PASSWORD' => $request->db_pass,
                'SESSION_DRIVER' => 'file',
                'APP_URL' => url('/'),
            ]);

            // 3. Clear Config Cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return response()->json([
                'success' => true,
                'step' => 2,
                'message' => 'Environment updated! Now running migrations...'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function migrate()
    {
        if (File::exists(storage_path('app/setup.lock'))) {
            return redirect()->route('login');
        }

        try {
            // Force reload config for this request
            Artisan::call('config:clear');

            // Run Migrations
            Artisan::call('migrate', ['--force' => true]);
            
            try {
                Artisan::call('db:seed', ['--force' => true]);
            } catch (Exception $e) {
                // Ignore seed errors if already seeded
            }

            // Create Lock File
            File::put(storage_path('app/setup.lock'), 'Setup completed at: ' . now());

            return response()->json([
                'success' => true,
                'message' => 'Setup finished successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Migration Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateEnv($data)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            $content = File::get($path);

            foreach ($data as $key => $value) {
                // Wrap in quotes if value has spaces or special characters
                $formattedValue = (str_contains($value, ' ') || preg_match('/[#@$!%*?&]/', $value)) 
                    ? "\"$value\"" 
                    : $value;
                
                // Un-comment the line if it was commented out, and update value
                if (preg_match("/^#?\s*{$key}=/m", $content)) {
                    $content = preg_replace("/^#?\s*{$key}=.*/m", "{$key}={$formattedValue}", $content);
                } else {
                    $content .= "\n{$key}={$formattedValue}";
                }
            }

            File::put($path, $content);
        }
    }
}
