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
     * Sync Blade templates to Database (FORCE OVERWRITE)
     */
    public function syncTemplatesToDatabase()
    {
        $templates = [
            'admin_new_order' => 'emails.v2.admin_new_order',
            'admin_payment_proof' => 'emails.v2.admin_payment_proof',
            'payment_approved' => 'emails.v2.client_payment_received',
            'order_status_updated' => 'emails.v2.client_order_update',
            'new_message_client' => 'emails.v2.client_new_message',
            'admin_notification' => 'emails.v2.admin_notification',
            'payment_failed' => 'emails.v2.payment_failed',
            'payment_refunded' => 'emails.v2.payment_refunded',
            'invoice_created' => 'emails.v2.invoice_created',
            'test_connection' => 'emails.v2.universal_v2',
            'announcement' => 'emails.v2.universal_v2',
            'order_confirmation' => 'emails.v2.universal_v2',
            'topup_pending' => 'emails.v2.universal_v2',
            'topup_approved' => 'emails.v2.universal_v2',
            'topup_rejected' => 'emails.v2.universal_v2',
        ];

        foreach ($templates as $slug => $view) {
            try {
                // Render the template with placeholders
                $content = view($view, [
                    'logo_url' => '{logo_url}',
                    'title' => '{title}',
                    'client_name' => '{client_name}',
                    'user_name' => '{user_name}',
                    'order_id' => '{order_id}',
                    'id' => '{id}',
                    'order_amount' => '{order_amount}',
                    'amount' => '{amount}',
                    'order_date' => '{order_date}',
                    'date' => '{date}',
                    'payment_date' => '{payment_date}',
                    'submission_date' => '{submission_date}',
                    'update_date' => '{update_date}',
                    'message_date' => '{message_date}',
                    'message_preview' => '{message_preview}',
                    'message' => '{message}',
                    'order_status' => '{order_status}',
                    'status' => '{status}',
                    'previous_status' => '{previous_status}',
                    'admin_panel_url' => '{admin_panel_url}',
                    'order_details_url' => '{order_details_url}',
                    'order_url' => '{order_url}',
                    'reply_url' => '{reply_url}',
                    'dashboard_portal_url' => '{dashboard_portal_url}',
                    'dashboard_url' => '{dashboard_url}',
                    'contact_url' => '{contact_url}',
                    'terms_url' => '{terms_url}',
                    'privacy_url' => '{privacy_url}',
                    'refund_url' => '{refund_url}',
                    'inbox_url' => '{inbox_url}',
                    'year' => '{year}',
                ])->render();

                // FORCE UPDATE existing or create new
                \App\Models\EmailTemplate::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucwords(str_replace(['_', 'client'], [' ', ' (Client)'], $slug)),
                        'subject' => $this->getDefaultSubject($slug),
                        'body' => $content,
                        'type' => str_contains($slug, 'payment') ? 'payment' : 'order'
                    ]
                );
            } catch (\Exception $e) {
                if (class_exists('\Illuminate\Support\Facades\Log')) {
                    \Illuminate\Support\Facades\Log::error("Failed to sync template $slug: " . $e->getMessage());
                }
            }
        }
        return true;
    }

    private function getDefaultSubject($slug)
    {
        return match($slug) {
            'admin_new_order' => "[TrafficVai] New Order #{order_id} Received",
            'admin_payment_proof' => "[TrafficVai] Payment Proof Submitted for Order #{order_id}",
            'admin_notification' => "[TrafficVai] System Notification",
            'payment_approved' => "Payment Received for Order #{order_id}",
            'payment_failed' => "Payment Failed for Order #{order_id}",
            'payment_refunded' => "Refund Processed for Order #{order_id}",
            'order_status_updated' => "Update on your Order #{order_id}",
            'new_message_client' => "New Message for Order #{order_id}",
            'invoice_created' => "New Invoice Generated - TrafficVai",
            'test_connection' => "[TrafficVai] Connection Test Successful",
            'announcement' => "Important Announcement from TrafficVai",
            default => ucwords(str_replace('_', ' ', $slug))
        };
    }

    /**
     * Internal method to send email using database templates and dynamic SMTP
     */
    public function sendEmail($templateSlug, $recipient, $data = [])
    {
        try {
            // 1. Prepare Data for Replacement (Comprehensive coverage)
            $vars = [
                'logo_url' => \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : (config('app.url') . '/images/logo.png'),
                'title' => $data['title'] ?? ucwords(str_replace('_', ' ', $templateSlug)),
                'client_name' => $data['user_name'] ?? 'Client',
                'user_name' => $data['user_name'] ?? 'Client', // Alias
                'order_id' => $data['order_id'] ?? ($data['id'] ?? 'N/A'),
                'id' => $data['order_id'] ?? ($data['id'] ?? 'N/A'), // Alias
                'order_amount' => $data['amount'] ?? ($data['order_amount'] ?? 'N/A'),
                'amount' => $data['amount'] ?? ($data['order_amount'] ?? 'N/A'), // Alias
                'order_date' => $data['order_date'] ?? ($data['date'] ?? now()->format('M d, Y h:i A')),
                'date' => $data['order_date'] ?? ($data['date'] ?? now()->format('M d, Y h:i A')), // Alias
                'payment_date' => $data['payment_date'] ?? ($data['date'] ?? now()->format('M d, Y h:i A')),
                'submission_date' => $data['submission_date'] ?? now()->format('M d, Y h:i A'),
                'update_date' => $data['update_date'] ?? now()->format('M d, Y h:i A'),
                'message_date' => $data['message_date'] ?? now()->format('M d, Y h:i A'),
                'message_preview' => $data['message_preview'] ?? ($data['message'] ?? ''),
                'message' => $data['message_preview'] ?? ($data['message'] ?? ''), // Alias
                'order_status' => $data['status'] ?? ($data['order_status'] ?? 'pending'),
                'status' => $data['status'] ?? ($data['order_status'] ?? 'pending'), // Alias
                'previous_status' => $data['previous_status'] ?? 'N/A',
                'admin_panel_url' => isset($data['order_id']) ? route('admin.orders.show', $data['order_id']) : url('/admin'),
                'order_details_url' => isset($data['order_id']) ? route('client.orders.show', $data['order_id']) : url('/client/orders'),
                'order_url' => url('/client/orders'),
                'reply_url' => isset($data['order_id']) ? route('client.orders.show', $data['order_id']) : url('/client/orders'),
                'dashboard_url' => url('/dashboard'),
                'contact_url' => url('/contact'),
                'terms_url' => url('/terms'),
                'privacy_url' => url('/privacy-policy'),
                'refund_url' => url('/refund-policy'),
                'inbox_url' => url('/inbox'),
                'is_admin' => str_contains($templateSlug, 'admin'),
                'dashboard_portal_url' => str_contains($templateSlug, 'admin') ? url('/admin') : url('/dashboard'),
                'year' => date('Y'),
            ];

            // 2. Priority: Database Template (allows Admin to edit)
            $template = \App\Models\EmailTemplate::where('slug', $templateSlug)->first();
            $renderedHtml = null;
            $subject = ucwords(str_replace('_', ' ', $templateSlug));

            if ($template) {
                // AUTO-HEALING: If the DB template is plain-text (old version), force sync it with the beautiful V2 Blade view right now!
                if (!str_contains($template->body, '<html') && !str_contains($template->body, '<body')) {
                    $v2Mapping = [
                        'admin_new_order' => 'emails.v2.admin_new_order',
                        'admin_payment_proof' => 'emails.v2.admin_payment_proof',
                        'payment_approved' => 'emails.v2.client_payment_received',
                        'order_status_updated' => 'emails.v2.client_order_update',
                        'new_message_client' => 'emails.v2.client_new_message',
                        'admin_notification' => 'emails.v2.admin_notification',
                        'payment_failed' => 'emails.v2.payment_failed',
                        'payment_refunded' => 'emails.v2.payment_refunded',
                        'invoice_created' => 'emails.v2.invoice_created',
                        'test_connection' => 'emails.v2.universal_v2',
                        'announcement' => 'emails.v2.universal_v2',
                        'order_confirmation' => 'emails.v2.universal_v2',
                        'topup_pending' => 'emails.v2.universal_v2',
                        'topup_approved' => 'emails.v2.universal_v2',
                        'topup_rejected' => 'emails.v2.universal_v2',
                    ];

                    if (isset($v2Mapping[$templateSlug])) {
                        // Generate fresh HTML with placeholder ({ tags }) for the DB
                        $newBodyContent = view($v2Mapping[$templateSlug], [
                            'logo_url' => '{logo_url}',
                            'title' => '{title}',
                            'client_name' => '{client_name}',
                            'user_name' => '{user_name}',
                            'order_id' => '{order_id}',
                            'id' => '{id}',
                            'order_amount' => '{order_amount}',
                            'amount' => '{amount}',
                            'order_date' => '{order_date}',
                            'date' => '{date}',
                            'payment_date' => '{payment_date}',
                            'submission_date' => '{submission_date}',
                            'update_date' => '{update_date}',
                            'message_date' => '{message_date}',
                            'message_preview' => '{message_preview}',
                            'message' => '{message}',
                            'order_status' => '{order_status}',
                            'status' => '{status}',
                            'previous_status' => '{previous_status}',
                            'admin_panel_url' => '{admin_panel_url}',
                            'order_details_url' => '{order_details_url}',
                            'order_url' => '{order_url}',
                            'reply_url' => '{reply_url}',
                            'dashboard_portal_url' => '{dashboard_portal_url}',
                            'dashboard_url' => '{dashboard_url}',
                            'contact_url' => '{contact_url}',
                            'terms_url' => '{terms_url}',
                            'privacy_url' => '{privacy_url}',
                            'refund_url' => '{refund_url}',
                            'inbox_url' => '{inbox_url}',
                            'year' => '{year}',
                        ])->render();
                        
                        $template->update(['body' => $newBodyContent]);
                        // Log the healing
                        if (class_exists('\Illuminate\Support\Facades\Log')) {
                            \Illuminate\Support\Facades\Log::info("Auto-healed outdated plain-text DB template: " . $templateSlug);
                        }
                    }
                }

                $subject = $template->subject;
                $body = $template->body; // This is now guaranteed to be HTML if it was healed

                foreach ($vars as $key => $value) {
                    if (is_string($value) || is_numeric($value)) {
                        $subject = str_replace('{' . $key . '}', (string)$value, $subject);
                        $body = str_replace('{' . $key . '}', (string)$value, $body);
                    }
                }
                
                // If the admin purposely saved plain text or it STILL failed to heal (somehow)
                if (!str_contains($body, '<html') && !str_contains($body, '<body')) {
                    $vars['body'] = $body;
                    $vars['subject'] = $subject;
                    $renderedHtml = view('emails.v2.generic', $vars)->render();
                } else {
                    $renderedHtml = $body;
                }
            } 
            // 3. Fallback: Blade Templates (V2)
            else {
                $v2Mapping = [
                    'admin_new_order' => 'emails.v2.admin_new_order',
                    'admin_payment_proof' => 'emails.v2.admin_payment_proof',
                    'payment_approved' => 'emails.v2.client_payment_received',
                    'order_status_updated' => 'emails.v2.client_order_update',
                    'new_message_client' => 'emails.v2.client_new_message',
                    'admin_notification' => 'emails.v2.admin_notification',
                    'payment_failed' => 'emails.v2.payment_failed',
                    'payment_refunded' => 'emails.v2.payment_refunded',
                    'invoice_created' => 'emails.v2.invoice_created',
                    'test_connection' => 'emails.v2.universal_v2',
                    'announcement' => 'emails.v2.universal_v2',
                    'order_confirmation' => 'emails.v2.universal_v2',
                    'topup_pending' => 'emails.v2.universal_v2',
                    'topup_approved' => 'emails.v2.universal_v2',
                    'topup_rejected' => 'emails.v2.universal_v2',
                ];

                if (isset($v2Mapping[$templateSlug])) {
                    $renderedHtml = view($v2Mapping[$templateSlug], $vars)->render();
                    $subject = $this->getDefaultSubject($templateSlug);
                    // Replace variables in subject manually since match/render doesn't do it for strings
                    foreach ($vars as $key => $value) {
                        $subject = str_replace('{' . $key . '}', $value, $subject);
                    }
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
