<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;

class OrderController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = auth()->user()->orders()
            ->with(['package.service', 'guestPostSite'])
            ->withCount(['messages as unread_messages_count' => function ($q) {
                $q->where('is_read', false)
                  ->whereHas('user', function ($sub) {
                      $sub->where('is_admin', true);
                  });
            }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->get();
        return view('client.orders.index', compact('orders'));
    }

    public function running(\Illuminate\Http\Request $request)
    {
        $query = auth()->user()->orders()
            ->with(['package.service', 'guestPostSite'])
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
            
        return view('client.orders.running', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id != auth()->id())
            abort(403);

        $order->load(['package.service.requirements', 'requirements.serviceRequirement', 'guestPostSite', 'messages.user']);

        // Mark admin messages as read
        $order->messages()
            ->where('is_read', false)
            ->whereHas('user', function ($q) {
            $q->where('is_admin', true);
        })->update(['is_read' => true]);

        return view('client.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->user_id != auth()->id())
            abort(403);

        if ($order->package) {
            $order->load('package.service.requirements');
            $rules = [];
            foreach ($order->package->service->requirements as $req) {
                $rules['requirements.' . $req->id] = $req->is_required ? 'required|string' : 'nullable|string';
                if ($req->type === 'url') {
                    $rules['requirements.' . $req->id] .= '|url';
                }
            }
            $validated = $request->validate($rules);
            foreach ($validated['requirements'] as $reqId => $val) {
                $order->requirements()->updateOrCreate(
                ['service_requirement_id' => $reqId],
                ['value' => $val]
                );
            }
        }
        else if ($order->guestPostSite) {
            $rules = [
                'guest_post_url' => 'required|url|max:255',
                'guest_post_anchor' => 'required|string|max:255',
            ];

            if ($order->service_tier === 'placement') {
                $rules['article_body'] = 'required|string';
            }

            $validated = $request->validate($rules);
            $order->update($validated);
        }

        $expectedDeliveryDate = null;
        if ($order->package && $order->package->turnaround_time_days) {
            $days = (int) $order->package->turnaround_time_days;
            if ($order->is_emergency) {
                $days = (int) ($order->package->express_turnaround_time_days ?? ceil($days / 2));
            }
            $expectedDeliveryDate = now()->addDays($days);
        }
        elseif ($order->guestPostSite) {
            $days = (int) ($order->guestPostSite->delivery_time_days ?: 7); // Fallback to 7 days
            if ($order->is_emergency) {
                $days = (int) ($order->guestPostSite->express_delivery_time_days ?: ceil($days / 2));
            }
            $expectedDeliveryDate = now()->addDays($days);
        }

        $newStatus = 'processing';
        if ($order->status === 'pending_payment') {
            $newStatus = 'pending_payment';
        }

        $order->update([
            'status' => $newStatus,
            'expected_delivery_date' => $expectedDeliveryDate
        ]);

        return redirect()->route('client.orders.show', $order)->with('success', 'Requirements submitted successfully. We are now processing your order.');
    }

    public function submitProof(Request $request, Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'transaction_id' => 'nullable|string|max:255',
            'sender_number' => 'required|string|max:20',
            'payment_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = [
            'transaction_id' => $request->transaction_id,
            'sender_number' => $request->sender_number,
        ];

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $data['payment_proof'] = $path;
        }

        $order->update($data);

        return redirect()->route('client.orders.show', $order)->with('success', 'Payment details submitted successfully. Our team will verify and activate your order shortly.');
    }
    public function invoice(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $order->load(['package.service', 'guestPostSite', 'addons']);
        return view('client.orders.invoice', compact('order'));
    }
}
