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
            // ... (PDO connection and env update logic)

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
