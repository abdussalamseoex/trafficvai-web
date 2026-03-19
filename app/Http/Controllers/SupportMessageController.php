<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportMessageController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'message' => 'nullable|string|max:2000',
            'client_id' => $user->is_admin ? 'required|exists:users,id' : 'nullable',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if (!$validated['message'] && !$request->hasFile('attachment')) {
            return response()->json(['status' => 'error', 'message' => 'Message or attachment is required.'], 422);
        }

        $clientId = $user->is_admin ? $validated['client_id'] : $user->id;

        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('direct_attachments', 'public');
        }

        $messageText = $validated['message'] ?? $request->get('message');

        /** @var \App\Models\DirectMessage $message */
        $message = \App\Models\DirectMessage::create([
            'client_id' => $clientId,
            'sender_id' => auth()->id(),
            'message' => $messageText ?: 'Sent an attachment',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        // Send Email Notification
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            $clientUser = \App\Models\User::find($clientId);

            if ($user->is_admin) {
                // Admin replied to Client
                $notificationService->send('support_ticket_reply', $clientUser, [
                    'sender_name' => $user->name,
                    'message_preview' => \Illuminate\Support\Str::words($messageText, 20),
                    'link' => route('inbox')
                ]);
            } else {
                // Client replied to Admin
                $notificationService->notifyAdmin(
                    'New Support Reply from ' . $clientUser->name,
                    "Client {$clientUser->name} has sent a new message in Support/Direct Inbox.\n\nMessage: " . \Illuminate\Support\Str::words($messageText, 30),
                    route('inbox', ['user' => $clientUser->id])
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Support Reply Email Error: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => [
                    'id' => $message->id,
                    'sender_name' => auth()->user()->name,
                    'message' => $message->message,
                    'is_self' => true,
                    'created_at' => $message->created_at->diffForHumans(),
                    'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                    'attachment_name' => $message->attachment_name,
                ]
            ]);
        }

        return back()->with('success', 'Message sent.');
    }
}
