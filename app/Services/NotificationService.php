<?php

namespace App\Services;

use App\Models\NotificationHub;

class NotificationService
{
    /**
     * Notify Admin
     */
    /**
     * Notify Admin
     */
    public function notifyAdmin($title, $message, $link = null, $slug = 'admin_notification', $orderId = null)
    {
        \App\Models\NotificationHub::create([
            'user_id' => null, // null means it's for admins
            'title' => $title,
            'message' => $message,
            'type' => 'general',
            'link' => $link
        ]);

        // Also send email to all staff members
        $staff = \App\Models\User::where('is_admin', true)
            ->orWhereIn('role', ['manager', 'seo_expert', 'writer'])
            ->get();

        foreach ($staff as $member) {
            $this->sendEmail($slug, $member->email, [
                'user_name' => $member->name,
                'title' => $title,
                'message' => $message,
                'link' => $link ?? url('/'),
                'order_id' => $orderId
            ]);
        }
    }

    /**
     * Send notification to a specific user (and conditionally email if settings allow)
     */
    public function send($templateSlug, $user, $data = [])
    {
        $title = ucwords(str_replace('_', ' ', $templateSlug));
        $message = "You have a new update event: " . $title;

        if (isset($data['order_id'])) {
            $message = "Update on Order #{$data['order_id']}: {$title}";
        }

        $type = 'general';
        if (str_contains($templateSlug, 'order')) {
            $type = 'order';
        } elseif (str_contains($templateSlug, 'payment')) {
            $type = 'payment';
        } elseif (str_contains($templateSlug, 'message')) {
            $type = 'message';
        }

        \App\Models\NotificationHub::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $data['link'] ?? null
        ]);

        // Email Implementation
        $this->sendEmail($templateSlug, $user->email, $data);
    }

    /**
     * Internal method to send email using database templates and dynamic SMTP
     */
    public function sendEmail($templateSlug, $recipient, $data = [])
    {
        try {
            // 1. Prepare Data for Blade Templates
            $vars = [
                'logo_url' => config('app.url') . '/images/logo.png',
                'client_name' => $data['user_name'] ?? 'Client',
                'order_id' => $data['order_id'] ?? 'N/A',
                'order_amount' => $data['amount'] ?? ($data['order_amount'] ?? 'N/A'),
                'order_date' => $data['order_date'] ?? ($data['date'] ?? now()->format('M d, Y h:i A')),
                'payment_date' => $data['payment_date'] ?? ($data['date'] ?? now()->format('M d, Y h:i A')),
                'submission_date' => $data['submission_date'] ?? now()->format('M d, Y h:i A'),
                'update_date' => $data['update_date'] ?? now()->format('M d, Y h:i A'),
                'message_date' => $data['message_date'] ?? now()->format('M d, Y h:i A'),
                'message_preview' => $data['message_preview'] ?? '',
                'order_status' => $data['status'] ?? ($data['order_status'] ?? 'pending'),
                'previous_status' => $data['previous_status'] ?? 'N/A',
                'admin_panel_url' => isset($data['order_id']) ? route('admin.orders.show', $data['order_id']) : url('/admin'),
                'order_details_url' => isset($data['order_id']) ? route('client.orders.show', $data['order_id']) : url('/orders'),
                'order_url' => url('/orders'),
                'reply_url' => isset($data['order_id']) ? route('client.orders.show', $data['order_id']) : url('/orders'),
                'is_admin' => str_contains($templateSlug, 'admin'),
                'subject' => ucwords(str_replace('_', ' ', $templateSlug)),
            ];

            // 2. Custom Blade Templates check (V2)
            $v2Mapping = [
                'admin_new_order' => 'emails.v2.admin_new_order',
                'admin_payment_proof' => 'emails.v2.admin_payment_proof',
                'payment_approved' => 'emails.v2.client_payment_received',
                'order_status_updated' => 'emails.v2.client_order_update',
                'new_message_client' => 'emails.v2.client_new_message',
            ];

            $renderedHtml = null;
            $subject = $vars['subject'];

            if (isset($v2Mapping[$templateSlug])) {
                $renderedHtml = view($v2Mapping[$templateSlug], $vars)->render();
                // Set specific subjects for V2
                $subject = match($templateSlug) {
                    'admin_new_order' => "[TrafficVai] New Order #{$vars['order_id']} Received",
                    'admin_payment_proof' => "[TrafficVai] Payment Proof Submitted for Order #{$vars['order_id']}",
                    'payment_approved' => "Payment Received for Order #{$vars['order_id']}",
                    'order_status_updated' => "Update on your Order #{$vars['order_id']}",
                    'new_message_client' => "New Message for Order #{$vars['order_id']}",
                    default => $subject
                };
            } else {
                // 3. Load from Database (Fallback)
                $template = \App\Models\EmailTemplate::where('slug', $templateSlug)->first();
                
                if (!$template && $templateSlug === 'test_connection') {
                    $template = \App\Models\EmailTemplate::create([
                        'slug' => 'test_connection',
                        'name' => 'SMTP Test Connection',
                        'subject' => 'Test Email from {host}',
                        'body' => '<p>Hello {user_name},</p><p>This is a test email sent from <strong>{host}</strong> at {time}.</p><p>If you are reading this, your SMTP settings are working correctly!</p>',
                        'type' => 'general'
                    ]);
                }

                if ($template) {
                    $subject = $template->subject;
                    $body = $template->body;

                    foreach ($data as $key => $value) {
                        if (is_string($value) || is_numeric($value)) {
                            $subject = str_replace('{' . $key . '}', $value, $subject);
                            $body = str_replace('{' . $key . '}', $value, $body);
                        }
                    }
                    
                    // Wrap in Generic Template
                    $vars['body'] = $body;
                    $vars['subject'] = $subject;
                    $renderedHtml = view('emails.v2.generic', $vars)->render();
                }
            }

            if (!$renderedHtml) {
                return false;
            }

            // 4. Configure SMTP Dynamically
            $this->applyMailConfig();

            // 5. Send Mail
            \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\DynamicNotificationMail($subject, $renderedHtml));

            // 6. Log Success
            \App\Models\EmailLog::create([
                'recipient' => $recipient,
                'subject' => $subject,
                'template_id' => $template->id ?? null,
                'status' => 'sent',
                'payload' => $data
            ]);

            return true;
        } catch (\Exception $e) {
            // Log Failure
            \App\Models\EmailLog::create([
                'recipient' => $recipient,
                'subject' => $templateSlug,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'payload' => $data
            ]);
            return false;
        }
    }

    /**
     * Apply SMTP settings from database to Laravel config
     */
    public function applyMailConfig()
    {
        $settings = \App\Models\Setting::where('group', 'Email Settings')->pluck('value', 'key');

        if ($settings->get('mail_host')) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $settings->get('mail_host'),
                'mail.mailers.smtp.port' => $settings->get('mail_port'),
                'mail.mailers.smtp.encryption' => ($settings->get('mail_encryption') === 'none' || !$settings->get('mail_encryption')) ? null : $settings->get('mail_encryption'),
                'mail.mailers.smtp.username' => $settings->get('mail_username'),
                'mail.mailers.smtp.password' => $settings->get('mail_password'),
                'mail.from.address' => $settings->get('mail_from_address'),
                'mail.from.name' => $settings->get('mail_from_name'),
            ]);

            // Force refresh the transport
            \Illuminate\Support\Facades\Mail::purge('smtp');
        }
    }
}
