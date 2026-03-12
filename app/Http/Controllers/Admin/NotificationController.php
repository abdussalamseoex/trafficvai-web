<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $stats = [
            'total_notifications' => \App\Models\NotificationHub::count(),
            'total_emails_sent' => \App\Models\EmailLog::where('status', 'sent')->count(),
            'total_emails_failed' => \App\Models\EmailLog::where('status', 'failed')->count(),
            'recent_notifications' => \App\Models\NotificationHub::with('user')->latest()->take(10)->get(),
        ];

        return view('admin.notifications.index', compact('stats'));
    }

    public function logs()
    {
        $logs = \App\Models\EmailLog::with('template')->latest()->paginate(20);
        return view('admin.notifications.logs', compact('logs'));
    }

    public function settings()
    {
        $settings = \App\Models\Setting::where('group', 'Email Settings')->get();
        return view('admin.notifications.settings', compact('settings'));
    }

    public function updateSettings(\Illuminate\Http\Request $request)
    {
        foreach ($request->settings as $key => $value) {
            \App\Models\Setting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Email settings updated successfully.');
    }
}
