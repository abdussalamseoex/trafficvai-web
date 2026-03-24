<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Order::with('user', 'package.service')
            ->withCount(['messages as unread_messages_count' => function ($q) {
                $q->where('is_read', false)
                  ->whereHas('user', function ($sub) {
                      $sub->where('is_admin', false);
                  });
            }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->get();
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

    public function extendTime(Request $request, \App\Models\Order $order)
    {
        $validated = $request->validate([
            'added_days' => 'required|integer|min:1',
            'reason' => 'required|string|max:1000',
        ]);

        if (!$order->expected_delivery_date) {
            return back()->with('error', 'This order does not have a delivery date set yet.');
        }

        // Add the days to the expected_delivery_date
        $order->expected_delivery_date = $order->expected_delivery_date->addDays($validated['added_days']);
        $order->save();

        // Save Extension Log
        \App\Models\OrderExtension::create([
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'added_days' => $validated['added_days'],
            'reason' => $validated['reason'],
        ]);

        // Notify Client
        $message = "Your order delivery time has been extended by {$validated['added_days']} days. Reason: {$validated['reason']}. New Delivery Date: " . $order->expected_delivery_date->format('M d, Y h:i A');
        app(\App\Services\NotificationService::class)->send('order_status_updated', $order->user, [
            'order_id' => $order->id,
            'title' => 'Order Delivery Time Extended',
            'status' => $order->status,
            'message' => $message,
            'link' => route('client.orders.show', $order->id)
        ]);

        return back()->with('success', 'Order delivery time extended successfully.');
    }
}
