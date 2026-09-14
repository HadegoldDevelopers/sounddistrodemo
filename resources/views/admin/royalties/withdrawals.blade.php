@extends('layouts.admin.app')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
            Withdrawal Requests
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
                    <th class="px-6 py-3 text-left font-semibold">Actions</th>
                    <th class="px-6 py-3 text-left font-semibold">Details</th>
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

                        <td class="px-6 py-4 flex gap-2">
                            @if($withdrawal->status === 'pending')
                                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Approve
                                    </button>
                                </form>
                            @endif


                            @if($withdrawal->status !== 'paid')
                                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </td>
                    <td x-data="{ open: false }">
    <button @click="open = true" class="px-3 py-1 bg-blue-600 text-white rounded">
        Details
    </button>
<!--- Details Modal -->
    <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900">Withdrawal Details</h3>

            <p><strong>User:</strong> {{ $withdrawal->user->name }}</p>
            <p><strong>Email:</strong> {{ $withdrawal->user->email }}</p>
            <p><strong>Method:</strong> {{ ucfirst($withdrawal->method) }}</p>
            <p><strong>Amount:</strong> ${{ number_format($withdrawal->amount, 2) }}</p>

            <div>
                @if($withdrawal->method === 'bank')
                    <p><strong>Bank Info:</strong></p>
                    <pre class="bg-gray-100 p-2 rounded">{{ $withdrawal->details['info'] ?? '-' }}</pre>
                @elseif($withdrawal->method === 'paypal')
                    <p><strong>PayPal Email:</strong> {{ $withdrawal->details['paypal_email'] ?? '-' }}</p>
                @elseif($withdrawal->method === 'crypto')
                    <p><strong>Wallet:</strong> {{ $withdrawal->details['crypto_wallet'] ?? '-' }}</p>
                    {{-- optionally show network --}}
                @endif
            </div>

            <div class="mt-4 flex justify-end">
                <button @click="open = false" class="px-4 py-2 bg-red-600 text-white rounded">
                    Close
                </button>
            </div>
        </div>
    </div>
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No withdrawal requests found.
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
<div x-data="{ open: false }">
    <button @click="open = true" class="px-3 py-1 bg-blue-600 text-white rounded">
        Details
    </button>

    <div x-show="open" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Withdrawal Details</h3>

            <p><strong>User:</strong> {{ $withdrawal->user->name }}</p>
            <p><strong>Email:</strong> {{ $withdrawal->user->email }}</p>
            <p><strong>Method:</strong> {{ ucfirst($withdrawal->method) }}</p>
            <p><strong>Amount:</strong> ${{ number_format($withdrawal->amount, 2) }}</p>

            @if($withdrawal->method === 'bank')
                <p><strong>Bank Info:</strong></p>
                <pre class="bg-gray-100 p-2 rounded">{{ $withdrawal->details['info'] ?? '-' }}</pre>
            @elseif($withdrawal->method === 'paypal')
                <p><strong>PayPal Email:</strong> {{ $withdrawal->details['paypal_email'] ?? '-' }}</p>
            @elseif($withdrawal->method === 'crypto')
                <p><strong>Wallet:</strong> {{ $withdrawal->details['crypto_wallet'] ?? '-' }}</p>
            @endif

            <div class="mt-4 flex justify-end">
                <button @click="open = false" class="px-4 py-2 bg-red-600 text-white rounded">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
                <div class="flex gap-2 pt-2">
                    @if($withdrawal->status === 'pending')
                        <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Approve
                            </button>
                        </form>
                    @endif

                    @if($withdrawal->status !== 'paid')
                        <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Reject
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No withdrawal requests found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $withdrawals, '__pager' => $withdrawals])
    </div>
</div>
@endsection
