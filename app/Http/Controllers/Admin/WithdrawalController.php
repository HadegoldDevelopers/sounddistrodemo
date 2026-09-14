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

// Approve Withdrawal
public function approve(Withdrawal $withdrawal)
{
    // Update status
    $withdrawal->update(['status' => 'paid']);

    // Notify user
    NotificationService::withdrawalApproved($withdrawal);

    return back()->with('success', 'Withdrawal approved.');
}

// Reject withdrawal + refund user
public function reject(Withdrawal $withdrawal)
{
    // Prevent double refund
    if ($withdrawal->status === 'paid') {
        return back()->with('error', 'Paid withdrawals cannot be refunded.');
    }

    $user = $withdrawal->user;

    // Refund user
    $user->wallet_balance += $withdrawal->amount;
    $user->save();

    // Update withdrawal status
    $withdrawal->update(['status' => 'rejected']);

    // Notify user
    NotificationService::withdrawalRejected($withdrawal);

    return back()->with('success', 'Withdrawal rejected and amount refunded to user.');
}
    

}
