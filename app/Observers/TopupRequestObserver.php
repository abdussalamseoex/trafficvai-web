<?php

namespace App\Observers;

use App\Models\TopupRequest;

class TopupRequestObserver
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the TopupRequest "created" event.
     */
    public function created(\App\Models\TopupRequest $topupRequest): void
    {
    // We notify admin when a topup enters 'pending' status (proof submitted)
    // Note: 'initiated' status is just the beginning of the flow.
    // But the user rule says "Topup request -> Admin notified"
    // I'll notify admin when it becomes 'pending'.
    }

    /**
     * Handle the TopupRequest "updated" event.
     */
    public function updated(\App\Models\TopupRequest $topupRequest): void
    {
        if ($topupRequest->wasChanged('status') && $topupRequest->status === 'pending') {
            $this->notificationService->notifyAdmin(
                "New Top-up Request Submitted",
                "User {$topupRequest->user->name} has requested a top-up of {$topupRequest->amount}",
                route('admin.payments.topups')
            );
        }
    }
}
