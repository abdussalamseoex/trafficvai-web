<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::where('is_admin', false)->withCount('orders')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(\App\Models\User $user)
    {
        $user->load([
            'orders.service',
            'orders.package',
            'orders.guestPostSite',
            'directMessages.sender',
            'trafficCampaigns' => fn($q) => $q->latest(),
            'trafficPointLogs' => fn($q) => $q->latest(),
        ]);

        // Mark direct messages from this client as read
        \App\Models\DirectMessage::where('client_id', $user->id)
            ->where('is_read', false)
            ->whereHas('sender', function ($q) {
            $q->where('is_admin', false);
        })->update(['is_read' => true]);

        return view('admin.users.show', compact('user'));
    }

    public function destroy(\App\Models\User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function adjustBalance(Request $request, \App\Models\User $user, \App\Services\Payments\WalletService $walletService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
        ]);

        try {
            if ($request->type === 'credit') {
                $walletService->credit($user, $request->amount, 'adjustment', $request->description);
                $msg = 'Added $' . number_format($request->amount, 2) . ' USD to client balance.';
            } else {
                $walletService->debit($user, $request->amount, 'adjustment', $request->description);
                $msg = 'Subtracted $' . number_format($request->amount, 2) . ' USD from client balance.';
            }
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function adjustPoints(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
        ]);

        try {
            if ($request->type === 'debit' && $user->traffic_points < $request->points) {
                return back()->with('error', 'Client does not have enough Traffic Points (' . number_format($user->traffic_points) . ' Pts available).');
            }

            if ($request->type === 'credit') {
                $user->increment('traffic_points', $request->points);
                $ptsSigned = $request->points;
                $msg = 'Added ' . number_format($request->points) . ' Traffic Points to client.';
            } else {
                $user->decrement('traffic_points', $request->points);
                $ptsSigned = -$request->points;
                $msg = 'Subtracted ' . number_format($request->points) . ' Traffic Points from client.';
            }

            \App\Models\TrafficPointLog::create([
                'user_id' => $user->id,
                'type' => 'adjustment',
                'points' => $ptsSigned,
                'cost_usd' => 0,
                'description' => 'Admin Adjustment: ' . $request->description,
                'status' => 'completed',
            ]);

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
