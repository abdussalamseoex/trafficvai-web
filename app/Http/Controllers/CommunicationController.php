<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Fetch Order-based conversations
        $orderQuery = \App\Models\Order::where(function ($q) use ($user) {
            if (!$user->is_admin) {
                $q->where('user_id', $user->id);
            }
        })
            ->whereHas('messages')
            ->with(['messages' => function ($q) {
            $q->latest();
        }, 'user', 'package.service', 'guestPostSite']);

        $orderConversations = $orderQuery->get();

        // 2. Fetch Direct conversations
        $directConversations = collect();
        if ($user->is_admin) {
            $dmGroups = \App\Models\DirectMessage::with(['sender', 'client'])
                ->get()
                ->groupBy('client_id');

            foreach ($dmGroups as $clientId => $msgs) {
                $lastMsg = $msgs->sortByDesc('created_at')->first();
                $client = $msgs->first()->client;
                $directConversations->push((object)[
                    'id' => $clientId,
                    'type' => 'direct',
                    'title' => ($client->name ?? 'Direct Chat'),
                    'client' => $client,
                    'last_message' => $lastMsg,
                    'created_at' => $lastMsg->created_at,
                    'unread_count' => $msgs->where('is_read', false)->where('sender_id', '!=', $user->id)->count()
                ]);
            }
        }
        else {
            // Client side direct
            $msgs = \App\Models\DirectMessage::where('client_id', $user->id)
                ->with(['sender', 'client'])
                ->get();

            if ($msgs->count() > 0) {
                $lastMsg = $msgs->sortByDesc('created_at')->first();
                $directConversations->push((object)[
                    'id' => $user->id,
                    'type' => 'direct',
                    'title' => 'TrafficVai Official Support',
                    'last_message' => $lastMsg,
                    'created_at' => $lastMsg->created_at,
                    'unread_count' => $msgs->where('is_read', false)->where('sender_id', '!=', $user->id)->count()
                ]);
            }
            else {
                // Default item for empty direct chat
                $directConversations->push((object)[
                    'id' => $user->id,
                    'type' => 'direct',
                    'title' => 'TrafficVai Official Support',
                    'last_message' => (object)['message' => 'Start a conversation with our team.', 'created_at' => now()],
                    'created_at' => now(),
                    'unread_count' => 0
                ]);
            }
        }

        // 3. Format Order Conversations
        $formattedOrders = $orderConversations->map(function ($order) {
            $lastMsg = $order->messages->first();
            $serviceTitle = 'Service Retrieval...';
            if ($order->package && $order->package->service) {
                $serviceTitle = $order->package->service->name;
            }
            elseif ($order->guestPostSite) {
                $serviceTitle = 'Guest Post: ' . $order->guestPostSite->site_url;
            }

            return (object)[
            'type' => 'order',
            'id' => $order->id,
            'title' => $serviceTitle . ' (#' . $order->id . ')',
            'service_title' => $serviceTitle,
            'order_id' => $order->id,
            'client' => $order->user,
            'last_message' => $lastMsg,
            'created_at' => $lastMsg ? $lastMsg->created_at : $order->created_at,
            'unread_count' => $order->messages->where('is_read', false)->where('user_id', '!=', auth()->id())->count()
            ];
        });

        // 4. Merge and Sort
        $allConversations = $formattedOrders->concat($directConversations)
            ->sortByDesc(function ($conv) {
            return $conv->created_at;
        })
            ->values();

        $view = $user->is_admin ? 'admin.communications.index' : 'client.communications.index';

        return view($view, ['conversations' => $allConversations]);
    }

    public function messages(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type');
        $id = $request->get('id');

        if ($type === 'order') {
            $order = \App\Models\Order::findOrFail($id);
            if (!$user->is_admin && $order->user_id != $user->id)
                abort(403);

            $messages = $order->messages()
                ->with('user')
                ->oldest()
                ->get()
                ->map(function ($m) use ($user) {
                return [
                'id' => $m->id,
                'message' => $m->message,
                'is_self' => $m->user_id === $user->id,
                'sender_name' => $m->user->name,
                'created_at' => $m->created_at->diffForHumans(),
                'full_date' => $m->created_at->format('M d, Y H:i A'),
                'attachment_path' => $m->attachment_path ? asset('storage/' . $m->attachment_path) : null,
                'attachment_name' => $m->attachment_name,
                'is_read' => (bool)$m->is_read,
                ];
            });

            $order->messages()
                ->where('user_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'messages' => $messages,
                'details' => [
                    'title' => ($order->package ? $order->package->service->name : 'Guest Post'),
                    'subtitle' => 'Order: #' . $order->id,
                    'link' => $user->is_admin ? route('admin.orders.show', $order) : route('client.orders.show', $order)
                ]
            ]);
        }
        else {
            // Direct
            if (!$user->is_admin && $id != $user->id)
                abort(403);

            $messages = \App\Models\DirectMessage::where('client_id', $id)
                ->with(['sender', 'client'])
                ->oldest()
                ->get()
                ->map(function ($m) use ($user) {
                return [
                'id' => $m->id,
                'message' => $m->message,
                'is_self' => $m->sender_id === $user->id,
                'sender_name' => $m->sender->name,
                'created_at' => $m->created_at->diffForHumans(),
                'full_date' => $m->created_at->format('M d, Y H:i A'),
                'attachment_path' => $m->attachment_path ? asset('storage/' . $m->attachment_path) : null,
                'attachment_name' => $m->attachment_name,
                'is_read' => (bool)$m->is_read,
                ];
            });

            $client = \App\Models\User::find($id);

            \App\Models\DirectMessage::where('client_id', $id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'messages' => $messages,
                'details' => [
                    'title' => $user->is_admin ? ($client->name ?? 'Direct Chat') : 'Expert Support (TrafficVai)',
                    'subtitle' => $user->is_admin ? 'Client Support Ticket' : 'Operational Dispatch Active',
                    'link' => $user->is_admin ? route('admin.users.show', $client) : '#'
                ]
            ]);
        }
    }
}
