<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the user's support tickets.
     */
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.support.index', compact('tickets'));
    }

    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'message' => 'required|string',
        ]);

        // Create the ticket
        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        // To make this fully functional, we are routing the first message to the 
        // polymorphic 'messages' table or a dedicated ticket_messages table.
        // For this UI implementation, we will just use the universal inbox system
        // by creating a Message connected to the Admin. Or simply a flash message
        // for now until a robust ticket messaging system is built.

        // As a placeholder, we just create the ticket record and show success.

        return redirect()->route('client.support.index')
            ->with('success', 'Your support ticket has been submitted successfully. We will respond shortly.');
    }
}
