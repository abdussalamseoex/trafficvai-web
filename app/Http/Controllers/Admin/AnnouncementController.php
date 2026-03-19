<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Jobs\SendBulkEmailJob;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of past announcements.
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store and optionally dispatch the bulk announcement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:email,notice,both',
            'action' => 'required|in:draft,send',
        ]);

        $announcement = Announcement::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => $request->type,
            'status' => $request->action === 'send' ? 'sent' : 'draft',
            'sent_at' => $request->action === 'send' ? now() : null,
        ]);

        if ($request->action === 'send') {
            // If it includes an email, dispatch the job
            if (in_array($announcement->type, ['email', 'both'])) {
                SendBulkEmailJob::dispatch($announcement);
                
                return redirect()->route('admin.announcements.index')
                    ->with('success', 'Announcement published and bulk emails are being dispatched!');
            } else {
                return redirect()->route('admin.announcements.index')
                    ->with('success', 'Dashboard Notice published successfully!');
            }
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement saved as draft.');
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Send a test email to the current admin.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = auth()->user();
        
        $sent = app(\App\Services\NotificationService::class)->sendEmail('announcement', $user->email, [
            'user_name' => $user->name,
            'title' => $request->subject,
            'message' => $request->message,
            'link' => url('/dashboard')
        ]);

        if ($sent) {
            return response()->json(['status' => 'success', 'message' => 'Test email sent to your address: ' . $user->email]);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Failed to send test email. Check SMTP settings.'], 500);
    }
}
