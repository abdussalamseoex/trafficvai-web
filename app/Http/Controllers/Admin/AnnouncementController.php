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
            'status' => 'draft',
        ]);

        if ($request->action === 'send') {

            // If it includes an email, dispatch the job
            if (in_array($announcement->type, ['email', 'both'])) {
                SendBulkEmailJob::dispatch($announcement);

                // Note: The job will update the status to 'sent' once finished
                return redirect()->route('admin.announcements.index')
                    ->with('success', 'Announcement saved and bulk emails are being sent in the background!');
            }
            else {
                // If it's pure notice, just mark as sent
                $announcement->update(['status' => 'sent', 'sent_at' => now()]);
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
}
