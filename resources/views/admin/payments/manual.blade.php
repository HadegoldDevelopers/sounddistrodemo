@extends('layouts.admin.app')

@section('title', 'Manual Payments')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Manual Payments</h2>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">User</th>
                    <th class="px-6 py-3 text-left font-semibold">Plan</th>
                    <th class="px-6 py-3 text-left font-semibold">Amount</th>
                    <th class="px-6 py-3 text-left font-semibold">Reference</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Requested At</th>
                    <th class="px-6 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $transaction->user->name ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $transaction->subscription_plan->name ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}</td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $transaction->reference }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-white
                                @if($transaction->status === 'pending') bg-yellow-600
                                @elseif($transaction->status === 'paid') bg-green-600
                                @else bg-red-600 @endif">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $transaction->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            @if($transaction->status === 'pending')
                                <form action="{{ route('admin.payments.manual.approve', $transaction) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Approve</button>
                                </form>
                                <form action="{{ route('admin.payments.manual.reject', $transaction) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No manual payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($transactions as $transaction)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <h3 class="font-semibold text-lg text-gray-900">{{ $transaction->user->name ?? '—' }}</h3>
                    <span class="px-2 py-1 rounded text-white text-xs
                        @if($transaction->status === 'pending') bg-yellow-600
                        @elseif($transaction->status === 'paid') bg-green-600
                        @else bg-red-600 @endif">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-700">
                    Plan: <span class="font-medium">{{ $transaction->subscription_plan->name ?? '—' }}</span>
                </p>
                <p class="text-sm text-gray-700">
                    Amount: <span class="font-medium">{{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}</span>
                </p>
                <p class="text-sm text-gray-500 font-mono">Ref: {{ $transaction->reference }}</p>
                <p class="text-sm text-gray-500">Requested: {{ $transaction->created_at->format('M d, Y') }}</p>

                @if($transaction->status === 'pending')
                    <div class="flex gap-2 pt-1">
                        <form action="{{ route('admin.payments.manual.approve', $transaction) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Approve</button>
                        </form>
                        <form action="{{ route('admin.payments.manual.reject', $transaction) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No manual payments found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $transactions, '__pager' => $transactions])
    </div>
</div>
@endsection