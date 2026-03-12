<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TopupRequest;
use App\Models\Setting;
use App\Services\Payments\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlisioCallbackController extends Controller
{
    /**
     * Handle the Plisio POST callback / IPN.
     *
     * Plisio sends a POST request with form fields including verify_hash.
     * We verify the signature, then update the Order or TopupRequest accordingly.
     */
    public function handleCallback(Request $request)
    {
        $post = $request->post();

        Log::info('Plisio Callback received', ['data' => $post]);

        // ── 1. Signature Verification ─────────────────────────────────────────
        if (!$this->verifyCallbackData($post)) {
            Log::error('Plisio Callback: invalid verify_hash or signature mismatch.', [
                'received_hash' => $post['verify_hash'] ?? 'none'
            ]);
            return response('Invalid signature', 400);
        }

        $status = $post['status'] ?? null;
        $orderNumber = $post['order_number'] ?? null;
        $txnId = $post['txn_id'] ?? null;

        if (!$orderNumber) {
            Log::error('Plisio Callback: missing order_number.');
            return response('OK', 200);
        }

        // ── 2. Route to Order or Topup ────────────────────────────────────────
        if (str_starts_with($orderNumber, 'ORDER_')) {
            $orderId = (int)str_replace('ORDER_', '', $orderNumber);
            $this->handleOrderCallback($orderId, $status, $txnId);
        }
        elseif (str_starts_with($orderNumber, 'TOPUP_')) {
            $topupId = (int)str_replace('TOPUP_', '', $orderNumber);
            $this->handleTopupCallback($topupId, $status, $txnId, $post);
        }
        else {
            Log::warning("Plisio Callback: unrecognised order_number format: {$orderNumber}");
        }

        // Always return 200 so Plisio stops retrying
        return response('OK', 200);
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Verify the callback's HMAC-SHA1 signature.
     *
     * @param array $post
     * @return bool
     */
    protected function verifyCallbackData(array $post): bool
    {
        if (!isset($post['verify_hash'])) {
            Log::warning('Plisio Callback: verify_hash is missing in POST data.');
            return false;
        }

        $secretKey = \App\Models\Setting::get('gateway_plisio_api_key', '');
        
        if (empty($secretKey)) {
            Log::error('Plisio Callback: Plisio API Key is not configured in settings.');
            return false;
        }

        $verifyHash = $post['verify_hash'];

        unset($post['verify_hash']);
        ksort($post);

        // Plisio requires expire_utc to be cast to string
        if (isset($post['expire_utc'])) {
            $post['expire_utc'] = (string)$post['expire_utc'];
        }

        // Plisio requires tx_urls to be html_entity_decoded
        if (isset($post['tx_urls'])) {
            $post['tx_urls'] = html_entity_decode($post['tx_urls']);
        }

        $serialized = serialize($post);
        $computed = hash_hmac('sha1', $serialized, $secretKey);

        $match = hash_equals($computed, $verifyHash);

        if (!$match) {
            Log::error('Plisio Callback: HMAC verification failed.', [
                'computed' => $computed,
                'received' => $verifyHash
            ]);
        }

        return $match;
    }

    /**
     * Handle callback for an Order payment.
     */
    protected function handleOrderCallback(int $orderId, ?string $status, ?string $txnId): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            Log::error("Plisio Callback: Order #{$orderId} not found.");
            return;
        }

        if ($status === 'completed') {
            if ($order->payment_status === 'paid') {
                Log::info("Plisio Callback: Order #{$orderId} already paid — skipping.");
                return;
            }

            $hasRequirements = $order->requirements()->count() > 0 || !empty($order->guest_post_url);
            $order->update([
                'status' => $hasRequirements ? 'processing' : 'pending_requirements',
                'payment_status' => 'paid',
                'payment_method' => 'plisio',
                'transaction_id' => $txnId,
            ]);

            Log::info("Plisio Callback: Order #{$orderId} marked as PAID. txn_id={$txnId}");

        }
        elseif (in_array($status, ['expired', 'cancelled', 'error'])) {
            if ($order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'failed']);
                Log::info("Plisio Callback: Order #{$orderId} marked as FAILED. status={$status}");
            }
        }
        else {
            Log::info("Plisio Callback: Order #{$orderId} status={$status} — no action taken.");
        }
    }

    /**
     * Handle callback for a Wallet Top-up.
     */
    protected function handleTopupCallback(int $topupId, ?string $status, ?string $txnId, array $post): void
    {
        $topup = TopupRequest::find($topupId);

        if (!$topup) {
            Log::error("Plisio Callback: TopupRequest #{$topupId} not found.");
            return;
        }

        if ($status === 'completed') {
            // Idempotency: check if already approved
            if ($topup->status === 'approved') {
                Log::info("Plisio Callback: TopupRequest #{$topupId} already approved — skipping.");
                return;
            }

            $user = \App\Models\User::find($topup->user_id);
            if (!$user) {
                Log::error("Plisio Callback: User not found for TopupRequest #{$topupId}.");
                return;
            }

            /** @var WalletService $walletService */
            $walletService = app(WalletService::class);

            $walletService->credit(
                $user,
                $topup->amount,
                'topup',
                "Wallet top-up via Plisio (Ref: {$txnId})",
            ['plisio_txn_id' => $txnId]
            );

            $topup->update([
                'status' => 'approved',
                'transaction_id' => $txnId,
            ]);

            Log::info("Plisio Callback: Wallet credited \${$topup->amount} for User #{$user->id}. txn_id={$txnId}");

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\WalletTopupReceipt([
                    'user_name' => $user->name,
                    'amount' => $topup->amount,
                    'payment_method' => 'plisio',
                    'transaction_id' => $txnId,
                    'date' => now()->format('M d, Y h:i A'),
                ])
                );
            }
            catch (\Exception $e) {
                Log::error('Mail Error (Plisio Callback): ' . $e->getMessage());
            }

        }
        elseif (in_array($status, ['expired', 'cancelled', 'error'])) {
            if ($topup->status !== 'approved') {
                $topup->update(['status' => 'rejected']);
                Log::info("Plisio Callback: TopupRequest #{$topupId} marked as REJECTED. status={$status}");
            }
        }
        else {
            Log::info("Plisio Callback: TopupRequest #{$topupId} status={$status} — no action taken.");
        }
    }
}
