<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::where('is_admin', false)->withCount('orders');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
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

                // Auto-resume ALL paused campaigns when admin adds points
                $pausedCampaigns = \App\Models\TrafficCampaign::where('user_id', $user->id)
                    ->where('status', 'paused')
                    ->get();

                if ($pausedCampaigns->count() > 0) {
                    $apiService = app(\App\Services\SurfEngineApiService::class);
                    $resumed = 0;
                    foreach ($pausedCampaigns as $camp) {
                        $resumeResp = $apiService->updateCampaignStatus($camp->external_order_id, 'active');
                        if ($resumeResp['success'] ?? false) {
                            $camp->status = 'active';
                            $camp->auto_paused = false;
                            $camp->save();
                            $resumed++;
                        }
                    }
                    if ($resumed > 0) {
                        $msg .= " {$resumed} paused campaign(s) auto-resumed.";
                    }
                }
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

    public function updatePassword(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        $emailSent = false;
        if ($request->boolean('notify_user')) {
            try {
                $emailBody = "Hello {$user->name},<br><br>Your TrafficVai account password has been updated by an administrator.<br><br><strong>Your New Password:</strong> <code>{$request->password}</code><br><br>You can log in to your account here:<br><a href=\"" . route('login') . "\">" . route('login') . "</a><br><br>After logging in, you may change your password anytime from your profile settings.<br><br>Best regards,<br>TrafficVai Team";

                $emailSent = app(\App\Services\NotificationService::class)->sendEmail(
                    'admin_notification',
                    $user->email,
                    [
                        'user_name' => $user->name,
                        'title' => 'Your Account Password Has Been Updated',
                        'message' => $emailBody,
                        'login_url' => route('login'),
                        'action_url' => route('login'),
                    ]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Admin update password email error: ' . $e->getMessage());
            }
        }

        $message = "Password updated successfully for {$user->name}.";
        if ($request->boolean('notify_user')) {
            $message .= $emailSent ? " Notification email sent to {$user->email}." : " (Email delivery failed or SMTP disabled).";
        }

        return back()
            ->with('success', $message)
            ->with('copied_password', $request->password);
    }

    public function sendResetLink(Request $request, \App\Models\User $user)
    {
        try {
            $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));

            $emailSent = false;
            try {
                $emailSent = app(\App\Services\NotificationService::class)->send('password_reset', $user, [
                    'action_url' => $resetUrl,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Send reset link error: ' . $e->getMessage());
            }

            $message = "Password reset link generated successfully!";
            if ($emailSent) {
                $message .= " An email has been dispatched to {$user->email}.";
            }

            return back()
                ->with('success', $message)
                ->with('generated_reset_url', $resetUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate reset link: ' . $e->getMessage());
        }
    }
}
