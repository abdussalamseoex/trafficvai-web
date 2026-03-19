<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function index()
    {
        $templates = [
            'admin_new_order' => 'Admin: New Order Received',
            'admin_payment_proof' => 'Admin: Payment Proof Submitted',
            'admin_notification' => 'Admin: System Notification (Tickets & Messages)',
            'payment_approved' => 'Client: Payment Approved',
            'payment_failed' => 'Client: Payment Failed',
            'payment_refunded' => 'Client: Payment Refunded',
            'order_status_updated' => 'Client: Order Status Updated',
            'new_message_client' => 'Client: New Message Received',
            'invoice_created' => 'Client & Admin: New Invoice Generated',
            'order_confirmation' => 'Client: Order Confirmation',
            'topup_pending' => 'Client: Wallet Top-up Pending',
            'topup_approved' => 'Client: Wallet Top-up Approved',
            'topup_rejected' => 'Client: Wallet Top-up Rejected',
            'ticket_status_updated' => 'Client: Support Ticket Status Updated',
            'announcement' => 'Client: Bulk Announcement',
            'test_connection' => 'Admin: SMTP Test Connection',
            'welcome_email' => 'Client: Welcome Email (Registration)',
            'password_reset' => 'Client: Password Reset',
            'staff_account_created' => 'Staff: Account Creation & Credentials',
            'support_ticket_reply' => 'Client: Support Ticket Reply'
        ];

        return view('admin.settings.notifications', compact('templates'));
    }

    public function update(Request $request)
    {
        $templates = [
            'admin_new_order', 'admin_payment_proof', 'admin_notification',
            'payment_approved', 'payment_failed', 'payment_refunded',
            'order_status_updated', 'new_message_client', 'invoice_created',
            'order_confirmation', 'topup_pending', 'topup_approved', 
            'topup_rejected', 'ticket_status_updated', 'announcement', 'test_connection',
            'welcome_email', 'password_reset', 'staff_account_created', 'support_ticket_reply'
        ];

        foreach ($templates as $slug) {
            $key = "email_toggle_{$slug}";
            $val = $request->has($key) ? '1' : '0';
            Setting::updateOrCreate(['key' => $key], ['value' => $val, 'group' => 'Email Notifications']);
        }
        
        // Clear Application Cache for settings
        if (function_exists('cache')) {
            cache()->forget('settings'); // or whatever caching is used
        }

        return back()->with('success', 'Notification delivery settings have been updated successfully.');
    }
}
