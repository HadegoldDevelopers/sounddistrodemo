@extends('layouts.admin.app')

@section('title', 'Withdrawal History')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
            Withdrawal History
        </h2>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Artist</th>
                    <th class="px-6 py-3 text-left font-semibold">Amount</th>
                    <th class="px-6 py-3 text-left font-semibold">Method</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Requested At</th>
                    <th class="px-6 py-3 text-left font-semibold">Updated At</th>
                </tr>
            </thead>

            <tbody>
                @forelse($withdrawals as $withdrawal)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $withdrawal->user->name }}
                        </td>

                        <td class="px-6 py-4">
                            ${{ number_format($withdrawal->amount, 2) }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $withdrawal->method ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-white
                                @if($withdrawal->status === 'pending') bg-yellow-600
                                @elseif($withdrawal->status === 'approved') bg-blue-600
                                @elseif($withdrawal->status === 'paid') bg-green-600
                                @else bg-red-600 @endif">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $withdrawal->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $withdrawal->updated_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No withdrawal history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($withdrawals as $withdrawal)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">

                <h3 class="font-semibold text-lg text-gray-900">{{ $withdrawal->user->name }}</h3>

                <p class="text-sm text-gray-700">
                    Amount: <span class="font-medium">${{ number_format($withdrawal->amount, 2) }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Method: <span class="font-medium">{{ $withdrawal->method ?? 'N/A' }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Status:
                    <span class="px-2 py-1 rounded text-white
                        @if($withdrawal->status === 'pending') bg-yellow-600
                        @elseif($withdrawal->status === 'approved') bg-blue-600
                        @elseif($withdrawal->status === 'paid') bg-green-600
                        @else bg-red-600 @endif">
                        {{ ucfirst($withdrawal->status) }}
                    </span>
                </p>

                <p class="text-sm text-gray-500">
                    Requested: {{ $withdrawal->created_at->format('M d, Y') }}
                </p>

                <p class="text-sm text-gray-500">
                    Updated: {{ $withdrawal->updated_at->format('M d, Y') }}
                </p>

            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No withdrawal history found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $withdrawals, '__pager' => $withdrawals])
    </div>

</div>
@endsection
