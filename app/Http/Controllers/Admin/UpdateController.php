<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use App\Models\UpdateLog;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    public function index()
    {
        $status = $this->updateService->getSystemStatus();
        $logs = UpdateLog::latest()->paginate(10);
        return view('admin.updates.index', compact('logs', 'status'));
    }

    public function check(Request $request)
    {
        $result = $this->updateService->checkForUpdates();
        
        if (isset($result['error'])) {
            return back()->with('error', 'Update Check Failed: ' . $result['error']);
        }

        if ($result['update_available']) {
            return back()->with([
                'update_available' => true,
                'local_version' => $result['local_version'],
                'remote_version' => $result['remote_version'],
                'changes' => $result['changes'],
                'success' => 'New updates are available on GitHub.'
            ]);
        }

        return back()->with([
            'success' => 'Your system is up to date.',
            'last_check_status' => true,
            'local_version' => $result['local_version'],
            'remote_version' => $result['remote_version'],
        ]);
    }

    public function update(Request $request)
    {
        $result = $this->updateService->applyUpdate();

        if ($result['success']) {
            return redirect()->route('admin.updates.index')->with('success', 'System updated successfully!');
        }

        return redirect()->route('admin.updates.index')->with('error', 'System update failed. Please check the logs.');
    }

    public function showLog(UpdateLog $log)
    {
        return view('admin.updates.log', compact('log'));
    }
}
