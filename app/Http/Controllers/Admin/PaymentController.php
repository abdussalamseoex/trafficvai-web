<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TopupRequest;
use App\Models\User;
use App\Services\Payments\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index()
    {
        $stats = [
            'total_revenue' => Transaction::where('type', 'credit')->where('source', '!=', 'refund')->sum('amount'),
            'total_topup' => Transaction::where('source', 'topup')->where('status', 'completed')->sum('amount'),
            'pending_topups' => TopupRequest::where('status', 'pending')->count(),
            'total_users_balance' => \App\Models\Wallet::sum('balance'),
        ];

        $recentTransactions = Transaction::with('user')->latest()->take(10)->get();

        return view('admin.payments.index', compact('stats', 'recentTransactions'));
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with('user')->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                }
                );
            });
        }

        $transactions = $query->paginate(20)->withQueryString();
        return view('admin.payments.transactions', compact('transactions'));
    }

    public function topups()
    {
        $requests = TopupRequest::with('user')->latest()->paginate(15);
        return view('admin.payments.topups', compact('requests'));
    }

    public function approveTopup(TopupRequest $topupRequest)
    {
        if ($topupRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($topupRequest) {
            $topupRequest->update(['status' => 'approved']);

            $this->walletService->credit(
                $topupRequest->user,
                $topupRequest->amount,
                'topup',
                'Manual Top-up approved by admin',
            ['request_id' => $topupRequest->id]
            );

            try {
                app(\App\Services\NotificationService::class)->send('topup_approved', $topupRequest->user, [
                    'amount' => $topupRequest->amount,
                    'title' => 'Top-up Approved',
                    'message' => "Your manual top-up of \${$topupRequest->amount} has been approved and added to your wallet.",
                    'link' => url('/client/payments')
                ]);
            }
            catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
            }

            // Check if this was an invoice settlement
            $invoiceId = $topupRequest->meta['invoice_id'] ?? null;
            if ($invoiceId) {
                $invoice = \App\Models\Invoice::find($invoiceId);
                if ($invoice) {
                    app(\App\Services\InvoiceService::class)->settle($invoice, $topupRequest->payment_method, $topupRequest->transaction_id ?? $topupRequest->id, "Admin Approval");
                }
            }
        });

        return back()->with('success', 'Top-up request approved successfully.');
    }

    public function rejectTopup(Request $request, TopupRequest $topupRequest)
    {
        if ($topupRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $topupRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        try {
            app(\App\Services\NotificationService::class)->send('topup_rejected', $topupRequest->user, [
                'amount' => $topupRequest->amount,
                'title' => 'Top-up Rejected',
                'message' => "Your top-up request for \${$topupRequest->amount} has been rejected. Reason: " . ($request->admin_note ?? 'Not provided') . ". Please contact support for further assistance.",
                'link' => url('/client/payments')
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail Error (Topup Rejected): ' . $e->getMessage());
        }

        return back()->with('success', 'Top-up request rejected.');
    }

    public function adjustWallet(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);

        try {
            if ($request->type === 'credit') {
                $this->walletService->credit($user, $request->amount, 'adjustment', $request->description);
            }
            else {
                $this->walletService->debit($user, $request->amount, 'adjustment', $request->description);
            }
            return back()->with('success', 'Wallet adjusted successfully.');
        }
        catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
