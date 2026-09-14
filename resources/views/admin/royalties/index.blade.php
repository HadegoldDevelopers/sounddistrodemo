@extends('layouts.admin.app')

@section('title', 'Royalties Reports')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
            Royalties Dashboard
        </h2>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 flex flex-wrap gap-4 items-center">
        <label for="range" class="font-semibold text-sm text-gray-700">Filter by:</label>

        <select name="range" id="range"
                class="p-2 border rounded w-full sm:w-auto">
            <option value="all" {{ request('range') == 'all' ? 'selected' : '' }}>All Time</option>
            <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>This Month</option>
            <option value="year" {{ request('range') == 'year' ? 'selected' : '' }}>This Year</option>
        </select>

        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Apply
        </button>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Streams</p>
            <p class="text-2xl font-bold text-blue-600">
                {{ number_format($stats['total_streams'] ?? 0) }}
            </p>
        </div>
        
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Unique Tracks</p>
            <p class="text-2xl font-bold text-indigo-600">
                {{ $stats['track_count'] ?? 0 }}
            </p>
        </div>
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Approved Earnings</p>
            <p class="text-2xl font-bold text-rose-600">
                {{ formatCurrency($approvedEarnings, 'USD')}}
            </p>
        </div>
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Payout to Artists ($)</p>
            <p class="text-2xl font-bold text-purple-600">
                 {{ formatCurrency($payoutToArtists , 'USD') }}
            </p>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Payout to Labels ($)</p>
            <p class="text-2xl font-bold text-yellow-600">
                {{ formatCurrency($payoutToLabels , 'USD') }}
            </p>
        </div>
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Earnings</p>
            <p class="text-2xl font-bold text-green-600">
                {{ formatCurrency($stats['total_earnings'] , 'USD') }}
            </p>
        </div>
    </div>

    <!-- Upload CSV Section -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 space-y-4">

        <h3 class="text-lg font-semibold text-indigo-900 mb-2">Auto-Detect CSV</h3>
        <!-- Simple Auto-Detect Instructions -->
<div class="bg-indigo-50 border-l-4 border-indigo-600 p-4 rounded-lg mb-6">
   
    <p class="text-indigo-800 text-sm">
        Upload your CSV and the system will automatically detect key columns: 
        <strong>Artist, Track, Streams, Earnings, Store, Country, Date, ISRC/UPC</strong>.
        Make sure your CSV headers match these names; otherwise, streams and earnings may default to 0.
    </p>
</div>


        <form action="{{ route('admin.royalties.reports.upload') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-4 max-w-lg">

            @csrf

            <div>
                <label for="distributor" class="block mb-1 font-semibold text-gray-700">
                    Choose Distributor:
                </label>
                <select name="distributor" id="distributor" required
                        class="w-full p-2 border rounded">
                    <option value="">-- Select Distributor --</option>
                    <option value="auto">Auto Detect</option>
                    <option value="soundrop">Soundrop</option>
                </select>
            </div>

            <div>
                <label for="earnings_csv" class="block mb-1 font-semibold text-gray-700">
                    Upload CSV Report:
                </label>
                <input type="file" name="earnings_csv" id="earnings_csv" required
                       class="w-full border rounded p-2">
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Upload
            </button>

        </form>

    </div>

</div>
@endsection
