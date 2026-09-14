<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Models\Stat;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{

    /**
     * Withdrawal Request Page
     */
    public function create()
    {
        $user = Auth::user();

        return view('user.withdrawal', [
            'user' => $user,
            'walletBalance' => $user->wallet_balance,
        ]);
    }

    /**
     * Submit Withdrawal Request
     */
    public function store(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:10',
        'method' => 'required|string',
        'details' => 'required|string',
    ]);

    $user = Auth::user();

    if ($request->amount > $user->wallet_balance) {
        return back()->with('error', 'Insufficient balance.');
    }

    // Deduct from wallet
    $user->wallet_balance -= $request->amount;
    $user->save();

    // Prepare details depending on method
    $details = [];

    if ($request->method === 'bank') {
        // Keep user-entered bank info as freeform string
        $details = ['info' => $request->details];
    } elseif ($request->method === 'paypal') {
        $details = [
            'paypal_email' => $request->details,
        ];
    } elseif ($request->method === 'crypto') {
        $details = [
            'crypto_wallet' => $request->details,
        ];
    }

    // Create withdrawal
    $withdrawal = Withdrawal::create([
        'user_id' => $user->id,
        'amount' => $request->amount,
        'method' => $request->method,
        'details' => $details,
        'status' => 'pending',
    ]);

    // Send notification to admin
    NotificationService::withdrawalRequested($withdrawal);

    return redirect()->route('royalties.index')
        ->with('success', 'Withdrawal request submitted successfully.');
}

    /**
     * Save Payout Information (Bank / PayPal / Crypto)
     */
    public function saveWithdrawal(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:30',
            'account_name' => 'nullable|string|max:255',
            'paypal_email' => 'nullable|email|max:255',
            'crypto_wallet' => 'nullable|string|max:255',
            'crypto_type' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        $user->bank_name = $request->bank_name;
        $user->account_number = $request->account_number;
        $user->account_name = $request->account_name;
        $user->paypal_email = $request->paypal_email;
        $user->crypto_wallet = $request->crypto_wallet;
        $user->crypto_type = $request->crypto_type;
        $user->save();

        return back()->with('success', 'Payout information updated successfully.');
    }
}
