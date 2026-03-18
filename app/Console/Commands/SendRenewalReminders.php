<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-renewal-reminders';

    protected $description = 'Generate draft invoices for orders nearing expiration and send reminders';

    public function handle()
    {
        $daysBefore = \App\Models\Setting::get('renewal_reminder_days', 7);
        $targetDate = now()->addDays($daysBefore)->startOfDay();

        $orders = \App\Models\Order::whereNotNull('expiry_date')
            ->where('status', 'processing')
            ->whereDate('expiry_date', '<=', $targetDate)
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            // Check if active renewal invoice already exists
            $existingInvoice = \App\Models\Invoice::where('order_id', $order->id)
                ->where('type', 'renewal')
                ->whereIn('status', ['draft', 'unpaid', 'overdue'])
                ->exists();

            if ($existingInvoice) {
                continue;
            }

            // Create Renewal Invoice
            $invoice = \App\Models\Invoice::create([
                'user_id' => $order->user_id,
                'invoice_number' => 'INV-REN-' . strtoupper(uniqid()),
                'subtotal' => $order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount,
                'total' => $order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount,
                'status' => 'unpaid',
                'type' => 'renewal',
                'order_id' => $order->id,
                'due_date' => $order->expiry_date,
                'notes' => 'Automated renewal generated for Order #' . $order->id,
            ]);

            $description = '';
            if ($order->package) {
                $description = 'Renewal for ' . ($order->package->service->name ?? 'Service') . ' - ' . $order->package->name . ' (Order #' . $order->id . ')';
            } elseif ($order->guestPostSite) {
                $description = 'Renewal for Guest Post - ' . $order->guestPostSite->url . ' (Order #' . $order->id . ')';
            } else {
                $description = 'Renewal for Custom Order #' . $order->id;
            }

            $invoice->items()->create([
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $invoice->subtotal,
                'total' => $invoice->subtotal,
            ]);

            // Notify User
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\InvoiceCreated($invoice));
                
                app(\App\Services\NotificationService::class)->send(
                    'invoice_created',
                    $order->user,
                    ['link' => route('client.invoices.show', $invoice)]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail/Notification Error during Renewal: ' . $e->getMessage());
            }

            $count++;
            $this->info("Generated renewal invoice for Order #{$order->id}");
        }

        $this->info("Renewal commands completed. Generated {$count} invoices.");
    }
}
