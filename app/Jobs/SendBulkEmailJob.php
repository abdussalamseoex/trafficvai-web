<?php

namespace App\Jobs;

use App\Mail\BulkAnnouncement;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $announcement;

    /**
     * Create a new job instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Note: Status is already set to 'sent' in the controller for dashboard visibility
        // $this->announcement->update(['status' => 'processing']);

        // Only send to actual clients (is_admin = 0 and role = 'client')
        $clients = User::where('is_admin', false)->where('role', 'client')->get();

        foreach ($clients as $client) {
            app(\App\Services\NotificationService::class)->sendEmail('announcement', $client->email, [
                'user_name' => $client->name,
                'title' => $this->announcement->subject,
                'message' => $this->announcement->message,
                'link' => url('/dashboard')
            ]);
        }

        // Mark as sent
        $this->announcement->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
