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

    // Future implementation: Queue Email sending job here based on email_templates
    }
}
