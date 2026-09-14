<?php

namespace App\Http\Controllers;

use App\Models\UserBalance;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    
    public function index()
{
    $user = Auth::user();

    // Pending earnings
    $pendingBalance = UserBalance::where('user_id', $user->id)
        ->pending()
        ->sum('amount');

    // Available balance (from user table)
    $availableBalance = $user->wallet_balance;

    // Total approved earnings (lifetime)
    $totalEarnings = UserBalance::where('user_id', $user->id)
        ->approved()
        ->sum('amount');

    // Earnings this month (approved)
    $monthlyEarnings = UserBalance::where('user_id', $user->id)
        ->approved()
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');

    // Earnings last 30 days (approved)
    $last30DaysEarnings = UserBalance::where('user_id', $user->id)
        ->approved()
        ->where('created_at', '>=', now()->subDays(30))
        ->sum('amount');

    // Royalty breakdown (all statuses, paginate)
   $royalties = UserBalance::with('music:id,title')
    ->where('user_id', $user->id)
    ->where('status', 'approved')
    ->selectRaw('
        music_id,
        month,
        year,
        SUM(amount) as earnings,
        SUM(streams) as streams,
        MAX(updated_at) as approved_at
    ')
    ->groupBy('music_id', 'month', 'year')
    ->orderByDesc('year')
    ->orderByDesc('month')
    ->paginate(5);



    // Withdrawal history
    $withdrawals = Withdrawal::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(5, ['*'], 'withdrawals_page');

    return view('user.royalties', compact(
        'user',
        'pendingBalance',
        'availableBalance',
        'totalEarnings',
        'monthlyEarnings',
        'last30DaysEarnings',
        'royalties',
        'withdrawals'
    ));
}

}
