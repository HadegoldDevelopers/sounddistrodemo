@extends('layouts.user')

@section('title', 'Withdraw Earnings')

@section('content')
<div class="max-w-xl mx-auto py-10 px-6">

    <h1 class="text-2xl font-bold text-white mb-6">Withdraw Earnings</h1>

    <!-- Local Currency Display -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mb-4">
        <p class="text-gray-400 text-sm">Your Balance (Local Currency)</p>
        <h2 class="text-3xl font-bold text-green-500 mt-2">
            {{ formatCurrency($user->wallet_balance) }}
        </h2>
    </div>

    <!-- USD Balance -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mb-6">
        <p class="text-gray-400 text-sm">Withdrawable Balance (USD)</p>
        <h2 class="text-3xl font-bold text-blue-400 mt-2">
            ${{ $user->wallet_balance }}
        </h2>

        <p class="text-gray-500 text-xs mt-3 leading-relaxed">
            Note: All withdrawals are processed in <span class="text-white font-semibold"> USD </span>.
            </p>
    </div>

    <form method="POST" action="{{ route('earnings.withdraw.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="text-gray-300 text-sm">Amount (USD)</label>
            <input type="number" name="amount" step="0.01" min="10"
                   class="w-full mt-1 p-3 bg-zinc-800 text-white rounded"
                   placeholder="Enter amount in USD"
                   required>
        </div>

        <div>
            <label class="text-gray-300 text-sm">Withdrawal Method</label>
            <select name="method" class="w-full mt-1 p-3 bg-zinc-800 text-white rounded" required>
                <option value="bank">Bank Transfer</option>
                <option value="paypal">PayPal</option>
                <option value="crypto">Crypto</option>
            </select>
        </div>

        <div>
            <label class="text-gray-300 text-sm">Details</label>
            <textarea name="details" class="w-full mt-1 p-3 bg-zinc-800 text-white rounded"
                      placeholder="Bank account, PayPal email, or crypto wallet address"
                      required></textarea>
        </div>

        <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded font-semibold">
            Submit Withdrawal Request
        </button>
    </form>

</div>
@endsection
