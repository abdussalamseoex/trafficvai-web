<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderMessageController extends Controller
{
    public function store(Request $request, Order $order)
    {
        Log::info('OrderMessage store attempt', ['order_id' => $order->id, 'user_id' => auth()->id(), 'has_file' => $request->hasFile('attachment')]);
        // Auth check: Is Admin OR is the client who owns the order
        if (!auth()->user()->is_admin && auth()->id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageText = $validated['message'] ?? $request->get('message');

        if (!$messageText && !$request->hasFile('attachment')) {
            Log::warning('OrderMessage store failed: empty content');
            return response()->json(['status' => 'error', 'message' => 'Message or attachment is required.'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('order_attachments', 'public');
        }

        /** @var \App\Models\OrderMessage $message */
        $message = $order->messages()->create([
            'user_id' => auth()->id(),
            'message' => $messageText ?: 'Sent an attachment',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);
        Log::info('OrderMessage created', ['id' => $message->id]);

        // If client sent the message, mark order as unread for admin
        if (!auth()->user()->is_admin) {
            $order->update(['is_read_admin' => false]);
        }
        else {
            // Admin sent the message, notify the user
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\NewMessageAlert([
                    'recipient_name' => $order->user->name,
                    'sender_name' => auth()->user()->name,
                    'message' => $message->message,
                    'link' => route('client.orders.show', $order)
                ]));
            }
            catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Message Alert Mail Error: ' . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => [
                    'id' => $message->id,
                    'user_name' => auth()->user()->name,
                    'message' => $message->message,
                    'is_self' => true,
                    'created_at' => $message->created_at->diffForHumans(),
                    'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                    'attachment_name' => $message->attachment_name,
                ]
            ]);
        }

        // Redirect back to the appropriate dashboard
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.orders.show', $order)->with('success', 'Message sent.');
        }

        return redirect()->route('client.orders.show', $order)->with('success', 'Message sent.');
    }
}
