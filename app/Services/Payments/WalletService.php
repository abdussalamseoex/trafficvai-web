<?php

namespace App\Services\Payments;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Credit funds to user wallet
     */
    public function credit(User $user, float $amount, string $source, string $description, array $meta = [])
    {
        return DB::transaction(function () use ($user, $amount, $source, $description, $meta) {
            $wallet = $this->ensureWalletExists($user);

            $wallet->increment('balance', $amount);

            return Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'source' => $source,
                'amount' => $amount,
                'description' => $description,
                'status' => 'completed',
                'meta' => $meta
            ]);
        });
    }

    /**
     * Debit funds from user wallet
     */
    public function debit(User $user, float $amount, string $source, string $description, array $meta = [])
    {
        return DB::transaction(function () use ($user, $amount, $source, $description, $meta) {
            $wallet = $this->ensureWalletExists($user);

            if ($wallet->balance < $amount) {
                throw new Exception("Insufficient wallet balance.");
            }

            $wallet->decrement('balance', $amount);

            return Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'source' => $source,
                'amount' => $amount,
                'description' => $description,
                'status' => 'completed',
                'meta' => $meta
            ]);
        });
    }

    /**
     * Ensure user has a wallet record
     */
    public function ensureWalletExists(User $user): Wallet
    {
        return Wallet::firstOrCreate(
        ['user_id' => $user->id],
        ['balance' => 0.00]
        );
    }
}
