<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;

class UserSubscriptionController extends Controller
{
    // Show all active subscribers
    public function active()
    {
        $subscribers = User::with('transactions')->where('is_sub', true)->paginate(10);

        return view('admin.subscriptions.active', compact('subscribers'));
    }

    // Show all subscription payments
    public function history()
    {
        $payments = Transaction::latest()->paginate(10);
        return view('admin.subscriptions.history', compact('payments'));
    }

    // Show subscription details for a specific user
   public function userSubscription(User $user)
{
    $payments = Transaction::where('user_id', $user->id)
        ->latest()
        ->paginate(10);

    return view('admin.subscriptions.user-history', compact('user', 'payments'));
}

}
