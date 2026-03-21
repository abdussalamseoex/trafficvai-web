<?php

namespace App\Jobs;

use App\Mail\CustomPromotionalEmail;
use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCustomBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $campaignId;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $campaignId)
    {
        $this->email = $email;
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Don't send if campaign doesn't exist
        $campaign = EmailCampaign::find($this->campaignId);
        if (!$campaign) return;

        try {
            Mail::to($this->email)->send(new CustomPromotionalEmail($campaign->subject, $campaign->message));
            
            // Increment the sent count safely tracking progress
            $campaign->increment('sent_count');
            
            // Check if all emails have been processed
            if ($campaign->sent_count >= $campaign->recipient_count) {
                $campaign->update(['status' => 'completed']);
            } else {
                // Ensure status shows as sending
                if ($campaign->status === 'completed_queueing') {
                    $campaign->update(['status' => 'sending']);
                }
            }
        } catch (\Exception $e) {
            $errorMsg = substr('Error: ' . $e->getMessage(), 0, 250);
            Log::error('Failed to send custom promotional email to ' . $this->email . ': ' . $e->getMessage());
            
            // Critical fail-safe: Update the UI so the user can see exactly why the email failed
            $campaign->update(['status' => $errorMsg]);
        }
    }
}
