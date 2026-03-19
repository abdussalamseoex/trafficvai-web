<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'package.service')
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
        $query = Order::with('user', 'package.service')
            ->whereNotNull('expiry_date')
            ->where('status', 'processing');

        $reminderDays = (int) Setting::get('renewal_reminder_days', 7);
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


        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }
}
