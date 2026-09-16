<?php

namespace App\Http\Controllers\Admin;

use App\Services\NotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;

class WithdrawalController extends Controller
{
    // Show only pending withdrawals
    public function index()
    {
        $withdrawals = Withdrawal::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.royalties.withdrawals', compact('withdrawals'));
    }

    // Show Withdrwal history (all statuses)
    public function history()
    {
        $withdrawals = Withdrawal::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.royalties.withdrawal-history', compact('withdrawals'));
    }

// Approve Withdrawal (idempotent — only pending can be marked paid)
public function approve(Withdrawal $withdrawal)
{
    $updated = \DB::transaction(function () use ($withdrawal) {
        return \App\Models\Withdrawal::query()
            ->where('id', $withdrawal->id)
            ->where('status', 'pending')
            ->update(['status' => 'paid']);
    });

    if ($updated === 0) {
        return back()->with('error', 'Only pending withdrawals can be approved.');
    }

    $withdrawal->status = 'paid';
    NotificationService::withdrawalApproved($withdrawal);

    return back()->with('success', 'Withdrawal approved.');
}

// Reject withdrawal + refund user (transaction-safe, no double refund)
public function reject(Withdrawal $withdrawal)
{
    $refunded = \DB::transaction(function () use ($withdrawal) {
        // Atomically flip pending → rejected. If another request already
        // rejected (or it was paid), nothing is updated and no refund occurs.
        $updated = \App\Models\Withdrawal::query()
            ->where('id', $withdrawal->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        if ($updated === 0) {
            return false;
        }

        $user = \App\Models\User::query()
            ->where('id', $withdrawal->user_id)
            ->lockForUpdate()
            ->first();

        if ($user) {
            $user->wallet_balance += $withdrawal->amount;
            $user->save();
        }

        return true;
    });

    if (!$refunded) {
        return back()->with('error', 'Paid or already-rejected withdrawals cannot be refunded.');
    }

    $withdrawal->status = 'rejected';
    NotificationService::withdrawalRejected($withdrawal);

    return back()->with('success', 'Withdrawal rejected and amount refunded to user.');
}
    

}
