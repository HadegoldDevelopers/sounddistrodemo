@extends('layouts.user')
@section('title', 'Earnings')

@section('content')
<div class="max-w-6xl mx-auto py-10 sm:px-6">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Earnings</h1>
        <p class="text-gray-400 mt-1">Track your royalties and withdraw your earnings</p>
    </div>

    {{-- Balance Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <p class="text-sm text-gray-400">Available Balance</p>
            <h2 class="text-2xl sm:text-3xl font-bold text-green-500 mt-2">
                {{ formatCurrency($availableBalance) }}
            </h2>
            <p class="text-xs text-gray-500 mt-2">Ready for withdrawal</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <p class="text-sm text-gray-400">Pending Balance</p>
            <h2 class="text-2xl sm:text-3xl font-bold text-yellow-500 mt-2">
                {{ formatCurrency($last30DaysEarnings) }}
            </h2>
            <p class="text-xs text-gray-500 mt-2">Processing from stores</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <p class="text-sm text-gray-400">Total Earned</p>
            <h2 class="text-2xl sm:text-3xl font-bold text-white mt-2">
                {{ formatCurrency($totalEarnings) }}
            </h2>
            <p class="text-xs text-gray-500 mt-2">Lifetime earnings</p>
        </div>
    </div>

    {{-- Withdraw Section --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-white">Withdraw Earnings</h3>
                <p class="text-sm text-gray-400 mt-1">
                    Minimum withdrawal: {{ formatCurrency(200) }}
                </p>
            </div>

            <div>
                @if(auth()->user()->wallet_balance >= 200)
                    <a href="{{ route('earnings.withdraw') }}"
                       class="w-full md:w-auto inline-flex justify-center items-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition">
                        <i class="fas fa-money-bill-wave mr-2"></i> Withdraw Now
                    </a>
                @else
                    <button disabled
                        class="w-full md:w-auto px-6 py-3 bg-zinc-700 text-gray-400 font-semibold rounded-lg cursor-not-allowed">
                        Insufficient Balance
                    </button>
                @endif
            </div>
        </div>
    </div>
<div class="space-y-8 md:space-y-10">
    {{-- Earnings Breakdown --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Earnings Breakdown</h3>

        {{-- Mobile Cards --}}
        <div class="space-y-4 md:hidden">
            @forelse($royalties as $earning)
                <div class="bg-zinc-800 rounded-lg p-4">
                    <p class="text-white font-semibold">
                        {{ $earning->music->title }}
                    </p>

                    <p class="text-sm text-gray-400 mt-1">
                        Streams: {{ number_format($earning->streams) }}
                    </p>

                    <p class="text-sm text-gray-400">
                     {{ $earning->approved_date->format('M d, Y') }}
                    </p>

                    <div class="flex justify-between items-center mt-3">
                        <span class="text-green-400 font-semibold">
                            {{ formatCurrency($earning->earnings, null, 7) }}
                        </span>

                       <span class="text-xs bg-green-600 px-2 py-1 rounded-full text-white">Paid</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">No earnings available yet</p>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-400">
                <thead class="text-xs uppercase bg-zinc-800 text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Release</th>
                        <th class="px-4 py-2">Track</th>
                        <th class="px-4 py-2">Streams</th>
                        <th class="px-4 py-2">Amount</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($royalties as $earning)
                        <tr class="border-b border-zinc-800 hover:bg-zinc-800/50">
                            <td class="px-4 py-3 text-white font-medium">
                                {{ $earning->music->title }}
                            </td>
                            <td class="px-4 py-3">{{ $earning->music->title }}</td>
                            <td class="px-4 py-3">{{ number_format($earning->streams) }}</td>
                            <td class="px-4 py-3 text-green-400 font-semibold">
                                {{ formatCurrency($earning->earnings, null, 7) }}
                            </td>
                            <td class="px-4 py-3">
                                
                                {{ $earning->approved_date->format('M d, Y') }}
                                
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">Paid</span>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                No earnings available yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($royalties->hasPages())
            <div class="mt-4">
                <x-pager :__pager="$royalties" />
            </div>
        @endif
    </div>

    {{-- Withdrawal History --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mt-10">
        <h3 class="text-lg font-semibold text-white mb-4">Withdrawal History</h3>

        {{-- Mobile Cards --}}
        <div class="space-y-4 md:hidden">
            @forelse($withdrawals as $withdrawal)
                <div class="bg-zinc-800 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-green-400 font-semibold">
                            {{ formatCurrency($withdrawal->amount) }}
                        </span>

                        @if($withdrawal->status === 'paid')
                            <span class="text-xs bg-green-600 px-2 py-1 rounded-full text-white">Paid</span>
                        @elseif($withdrawal->status === 'pending')
                            <span class="text-xs bg-yellow-600 px-2 py-1 rounded-full text-white">Pending</span>
                        @else
                            <span class="text-xs bg-red-600 px-2 py-1 rounded-full text-white">Rejected</span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-400 mt-1 capitalize">
                        Method: {{ $withdrawal->method }}
                    </p>

                    <p class="text-sm text-gray-400">
                        {{ $withdrawal->created_at->format('M d, Y') }}
                    </p>
                </div>
            @empty
                <p class="text-gray-500 text-center">No withdrawal history yet</p>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-400">
                <thead class="text-xs uppercase bg-zinc-800 text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Amount</th>
                        <th class="px-4 py-2">Method</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr class="border-b border-zinc-800 hover:bg-zinc-800/50">
                            <td class="px-4 py-3 text-green-400 font-semibold">
                                {{ formatCurrency($withdrawal->amount) }}
                            </td>
                            <td class="px-4 py-3 capitalize">{{ $withdrawal->method }}</td>
                            <td class="px-4 py-3">
                                @if($withdrawal->status === 'paid')
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">Paid</span>
                                @elseif($withdrawal->status === 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-600 text-white">Pending</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-600 text-white">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $withdrawal->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                No withdrawal history yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="mt-4">
                <x-pager :__pager="$withdrawals" />
            </div>
        @endif
    </div>
</div>
</div>
@endsection
