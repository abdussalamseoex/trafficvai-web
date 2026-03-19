<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\Payments\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Settle an invoice.
     * 
     * @param Invoice $invoice
     * @param string $paymentMethod
     * @param string|null $transactionId
     * @param string|null $notes
     * @return bool
     */
    public function settle(Invoice $invoice, string $paymentMethod, ?string $transactionId = null, ?string $notes = null): bool
    {
        if ($invoice->status === 'paid') {
            return true;
        }

        return DB::transaction(function () use ($invoice, $paymentMethod, $transactionId, $notes) {
            \Illuminate\Support\Facades\Log::info("Settling invoice #{$invoice->invoice_number}. Total before update: {$invoice->total}");

            // Update invoice status
            $invoice->update([
                'status' => 'paid',
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'payment_notes' => $notes,
            ]);

            \Illuminate\Support\Facades\Log::info("Invoice #{$invoice->invoice_number} updated to paid. Total after update: {$invoice->total}");

            // Notify Admin and Client
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                
                // Notify Client
                $notificationService->send('payment_approved', $invoice->user, [
                    'order_id' => $invoice->invoice_number,
                    'amount' => '$' . number_format($invoice->total, 2),
                    'title' => 'Invoice Payment Successful',
                    'date' => now()->format('M d, Y h:i A'),
                    'link' => route('client.invoices.show', $invoice->id)
                ]);

                // Notify Admin
                $notificationService->notifyAdmin(
                    'Invoice Paid',
                    "Invoice {$invoice->invoice_number} has been paid successfully by {$invoice->user->name} via " . ucfirst($paymentMethod) . ".",
                    route('admin.invoices.show', $invoice->id)
                );
            } catch (\Exception $e) {
                Log::error('Invoice Payment Notification Error: ' . $e->getMessage());
            }

            // If it's not a wallet payment, we still want to record the transaction for accounting
            // Wallet payments are already recorded as 'debit' by WalletService::debit()
            if ($paymentMethod !== 'wallet') {
                Transaction::create([
                    'user_id' => $invoice->user_id,
                    'type' => 'credit',
                    'source' => 'invoice_payment',
                    'amount' => $invoice->total,
                    'description' => "Payment for Invoice #{$invoice->invoice_number} via " . ucfirst($paymentMethod),
                    'status' => 'completed',
                    'meta' => [
                        'invoice_id' => $invoice->id,
                        'transaction_id' => $transactionId
                    ]
                ]);
            }

            Log::info("Invoice #{$invoice->invoice_number} settled via {$paymentMethod}.");
            return true;
        });
    }
}
