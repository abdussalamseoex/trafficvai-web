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

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $notificationService = app(\App\Services\NotificationService::class);
        
        $success = $notificationService->sendEmail('test_connection', $request->email, [
            'user_name' => 'Admin Test',
            'time' => now()->format('Y-m-d H:i:s'),
            'host' => \App\Models\Setting::get('mail_host'),
        ]);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Test email sent successfully! Please check your inbox.']);
        } else {
            $lastError = \App\Models\EmailLog::where('recipient', $request->email)->latest()->first();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send test email. Error: ' . ($lastError->error_message ?? 'Unknown error')
            ], 500);
        }
    }
}
