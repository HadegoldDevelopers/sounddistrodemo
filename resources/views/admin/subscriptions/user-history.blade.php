@extends('layouts.admin.app')

@section('title', 'Subscription History')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
        Subscription History — {{ $user->name }}
    </h2>

    <a href="{{ route('admin.subscriptions.active') }}"
       class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition">
        ← Back
    </a>
</div>


    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Amount</th>
                    <th class="px-6 py-3 text-left font-semibold">Currency</th>
                    <th class="px-6 py-3 text-left font-semibold">Original Currency</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-green-600 font-semibold">
                            {{ number_format($payment->amount, 2) }}
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $payment->currency }}
                        </td>
                        
                         <td class="px-6 py-4 text-gray-700">
                            {{ $payment->original_currency }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $payment->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No payment history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($payments as $payment)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">
                <p class="text-sm text-gray-700">
                    Amount: <span class="font-medium text-green-600 font-semibold">{{ number_format($payment->amount, 2) }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Currency: <span class="font-medium">{{ $payment->currency }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Status:
                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                        {{ ucfirst($payment->status) }}
                    </span>
                </p>

                <p class="text-sm text-gray-500">
                    Date: {{ $payment->created_at->format('M d, Y') }}
                </p>
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No payment history found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $payments, '__pager' => $payments])
    </div>

</div>
@endsection
