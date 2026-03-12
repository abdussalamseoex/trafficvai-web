<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of client announcements.
     */
    public function index()
    {
        $announcements = Announcement::whereIn('type', ['notice', 'both'])
            ->where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.announcements.index', compact('announcements'));
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        // Ensure it is for clients and sent
        if (!in_array($announcement->type, ['notice', 'both']) || $announcement->status !== 'sent') {
            abort(404);
        }

        return view('client.announcements.show', compact('announcement'));
    }
}
