<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of all support tickets for admin.
     */
    public function index()
    {
        $tickets = SupportTicket::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    /**
     * Display the specified ticket.
     * For now, just a simple view to see details and update status.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.support.show', compact('ticket'));
    }

    /**
     * Update the ticket status (e.g., mark as closed or in-progress).
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|string|in:open,in-progress,closed',
        ]);

        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => $request->status,
        ]);

        if ($oldStatus !== $request->status) {
            app(\App\Services\NotificationService::class)->send('ticket_status_updated', $ticket->user, [
                'title' => 'Support Ticket Update',
                'message' => "Your support ticket '{$ticket->subject}' has been updated. New Status: " . ucfirst($request->status) . ".",
                'link' => route('client.support.index')
            ]);
        }

        return redirect()->route('admin.support.show', $ticket->id)
            ->with('success', 'Ticket status updated successfully.');
    }
}
