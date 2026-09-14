@extends('layouts.admin.app')

@section('title', 'Earnings Report')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden">
    <!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Earnings Report</h2>

    <div class="flex gap-2">
        <!-- Mass Approve -->
        <form method="POST" id="mass-action-form">
    @csrf
    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700"
                formaction="{{ route('admin.royalties.earnings.mass.approve') }}">
            Approve Selected
        </button>

        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700"
                formaction="{{ route('admin.royalties.earnings.mass.reject') }}">
            Reject Selected
        </button>
    </div>
</form>
    </div>
</div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th class="px-6 py-3 text-left font-semibold">Artist</th>
                    <th class="px-6 py-3 text-left font-semibold">Track</th>
                    <th class="px-6 py-3 text-left font-semibold">Streams</th>
                    <th class="px-6 py-3 text-left font-semibold">Amount</th>
                    <th class="px-6 py-3 text-left font-semibold">Uploaded</th>
                    <th class="px-6 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pending as $row)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
    <input type="checkbox" name="ids[]" value="{{ $row->id }}" form="mass-action-form">
</td>

                        <td class="px-6 py-4 font-medium text-gray-900">
                           {{ $row->user?->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $row->music?->title ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ number_format($row->streams) }}
                        </td>

                        <td class="px-6 py-4 text-green-600 font-semibold">
                             {{ formatCurrency($row->amount, 'USD')}}
                        </td>
                        
                        <td class="px-6 py-4 text-gray-600">
                            {{ $row->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4 flex gap-2">
                            <form action="{{ route('admin.royalties.earnings.approve', $row->id) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    Approve
                                </button>
                            </form>

                            <form action="{{ route('admin.royalties.earnings.reject', $row->id) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No pending earnings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($pending as $row)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">
                <h3 class="font-semibold text-lg text-gray-900">
                    {{ $row->user?->name ?? '—' }}
                    </h3>

                <p class="text-sm text-gray-700">Track: <span class="font-medium">
                    {{ $row->music?->title ?? '—' }}
                    </span>
                </p>
                <p class="text-sm text-gray-700">Streams: <span class="font-medium">{{ number_format($row->streams) }}</span></p>
                <p class="text-sm text-gray-700">Amount: 
                    <span class="font-medium text-green-600">{{ formatCurrency($row->amount, null, 7) }}</span>
                </p>
                <p class="text-sm text-gray-500">Uploaded: {{ $row->created_at->format('M d, Y') }}</p>

                <div class="flex gap-2 pt-2">
                    <form action="{{ route('admin.royalties.earnings.approve', $row->id) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1 bg-green-600 text-white rounded">Approve</button>
                    </form>

                    <form action="{{ route('admin.royalties.earnings.reject', $row->id) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1 bg-red-600 text-white rounded">Reject</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No pending earnings found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $pending, '__pager' => $pending])
    </div>

</div>

<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

@endsection
