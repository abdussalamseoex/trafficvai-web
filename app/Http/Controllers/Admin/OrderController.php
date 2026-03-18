<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = \App\Models\Order::with('user', 'package.service')
            ->withCount(['messages as unread_messages_count' => function ($query) {
            $query->where('is_read', false)
                ->whereHas('user', function ($q) {
                $q->where('is_admin', false);
            }
            );
        }])
            ->latest()
            ->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function running(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Order::with('user', 'package.service')
            ->whereNotNull('expiry_date')
            ->where('status', 'processing');

        $reminderDays = \App\Models\Setting::get('renewal_reminder_days', 7);
        $targetDate = now()->addDays($reminderDays)->endOfDay();

        if ($request->filter === 'expiring_soon') {
            $query->where('expiry_date', '>', now())
                  ->where('expiry_date', '<=', $targetDate);
        } elseif ($request->filter === 'expired') {
            $query->where('expiry_date', '<', now());
        }

        $orders = $query->orderBy('expiry_date', 'asc')->get();
            
        return view('admin.orders.running', compact('orders'));
    }

    public function show(\App\Models\Order $order)
    {
        $order->load(['user', 'package.service.requirements', 'requirements.serviceRequirement', 'guestPostSite', 'messages.user']);

        // Mark order as read by admin
        if (!$order->is_read_admin) {
            $order->update(['is_read_admin' => true]);
        }

        // Mark client messages as read
        $order->messages()
            ->where('is_read', false)
            ->whereHas('user', function ($q) {
            $q->where('is_admin', false);
        })->update(['is_read' => true]);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, \App\Models\Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_payment,pending_requirements,processing,completed',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'report_file_path' => 'nullable|string',
            'published_url' => 'nullable|url',
            'expiry_date' => 'nullable|date'
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        if ($oldStatus === 'pending_payment' && $validated['payment_status'] === 'paid' && $order->status === 'pending_payment') {
            $hasRequirements = $order->requirements()->count() > 0 || !empty($order->guest_post_url);
            $order->update(['status' => $hasRequirements ? 'processing' : 'pending_requirements']);
        }

        if ($oldStatus !== $order->status) {
            $serviceTitle = 'Order Update';
            if ($order->package) {
                $serviceTitle = 'Service: ' . $order->package->service->title . ' - ' . $order->package->name;
            }
            elseif ($order->guestPostSite) {
                $serviceTitle = 'Guest Post: ' . $order->guestPostSite->site_name;
            }

            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusUpdated([
                    'id' => $order->id,
                    'user_name' => $order->user->name,
                    'title' => $serviceTitle,
                    'old_status' => str_replace('_', ' ', $oldStatus),
                    'new_status' => str_replace('_', ' ', $order->status)
                ]));
            }
            catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Order Status Mail Error: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }
}
