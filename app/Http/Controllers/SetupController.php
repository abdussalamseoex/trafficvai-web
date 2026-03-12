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
        // Double check lock
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
                'DB_HOST' => $request->db_host,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_user,
                'DB_PASSWORD' => $request->db_pass,
                'APP_URL' => url('/'), // Update APP_URL to current if needed
            ]);

            // 3. Clear Config Cache so new env is loaded
            Artisan::call('config:clear');
            
            // Re-configure connection for the current request
            config(['database.connections.mysql.host' => $request->db_host]);
            config(['database.connections.mysql.database' => $request->db_name]);
            config(['database.connections.mysql.username' => $request->db_user]);
            config(['database.connections.mysql.password' => $request->db_pass]);
            DB::purge('mysql');

            // 4. Run Migrations
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            // 5. Create Lock File
            File::put(storage_path('app/setup.lock'), 'Setup completed at: ' . now());

            return response()->json([
                'success' => true,
                'message' => 'Database configured, migrated, and setup locked successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateEnv($data)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            $content = File::get($path);

            foreach ($data as $key => $value) {
                // If value contains spaces, wrap it in quotes
                $formattedValue = str_contains($value, ' ') ? "\"$value\"" : $value;
                
                if (str_contains($content, "{$key}=")) {
                    $content = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $content);
                } else {
                    $content .= "\n{$key}={$formattedValue}";
                }
            }

            File::put($path, $content);
        }
    }
}
