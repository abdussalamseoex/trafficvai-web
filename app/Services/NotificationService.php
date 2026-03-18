<?php

namespace App\Services;

use App\Models\NotificationHub;

class NotificationService
{
    /**
     * Notify Admin
     */
    public function notifyAdmin($title, $message, $link = null)
    {
        NotificationHub::create([
            'user_id' => null, // null means it's for admins
            'title' => $title,
            'message' => $message,
            'type' => 'general',
            'link' => $link
        ]);
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
        }
        elseif (str_contains($templateSlug, 'payment')) {
            $type = 'payment';
        }
        elseif (str_contains($templateSlug, 'message')) {
            $type = 'message';
        }

        NotificationHub::create([
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
            // 1. Load Template
            $template = \App\Models\EmailTemplate::where('slug', $templateSlug)->first();
            
            // Fallback for test_connection if seeder wasn't run
            if (!$template && $templateSlug === 'test_connection') {
                $template = \App\Models\EmailTemplate::create([
                    'slug' => 'test_connection',
                    'name' => 'SMTP Test Connection',
                    'subject' => 'Test Email from {host}',
                    'body' => '<p>Hello {user_name},</p><p>This is a test email sent from <strong>{host}</strong> at {time}.</p><p>If you are reading this, your SMTP settings are working correctly!</p>',
                    'type' => 'general'
                ]);
            }

            if (!$template) {
                // If no template found, we can't send email
                return false;
            }

            // 2. Prepare Data (Variable Replacement)
            $subject = $template->subject;
            $body = $template->body;

            foreach ($data as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $subject = str_replace('{' . $key . '}', $value, $subject);
                    $body = str_replace('{' . $key . '}', $value, $body);
                }
            }

            // 3. Configure SMTP Dynamically
            $this->applyMailConfig();

            // 4. Send Mail
            \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\DynamicNotificationMail($subject, $body));

            // 5. Log Success
            \App\Models\EmailLog::create([
                'recipient' => $recipient,
                'subject' => $subject,
                'template_id' => $template->id,
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
                'mail.mailers.smtp.host' => $settings->get('mail_host'),
                'mail.mailers.smtp.port' => $settings->get('mail_port'),
                'mail.mailers.smtp.encryption' => $settings->get('mail_encryption') === 'none' ? null : $settings->get('mail_encryption'),
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
