@extends('layouts.admin.app')

@section('title', 'Streams Details')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Back Button -->
    <div class="flex justify-between items-center">
       <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
            Stream Detail for {{ $music->title }} by {{ $music->artist }}
        </h2>

        <a href="{{ route('admin.royalties.reports.streams') }}"
           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
            ← Back
        </a>
    </div>


    <!-- Page Header -->
    <div>
        

        <p class="text-sm text-gray-600 mt-1">
            Detailed breakdown of streams by song, platform, and country.
            | Month: {{ $period ?? 'All Time'}}
        </p>
    </div>

   <!-- Filters -->
<form method="GET" action="{{ route('admin.royalties.reports.streams.detail', ['music_id' => $music->id]) }}" class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <input type="hidden" name="music_id" value="{{ $music->id }}">

    <select name="store" class="p-2 border rounded w-full">
        <option value="">All Platforms</option>
        @foreach ($stores as $store)
            <option value="{{ $store }}" {{ request('store') == $store ? 'selected' : '' }}>
                {{ $store }}
            </option>
        @endforeach
    </select>

    <select name="country" class="p-2 border rounded w-full">
        <option value="">All Countries</option>
        @foreach ($countries as $country)
            <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                {{ $country }}
            </option>
        @endforeach
    </select>

    <div class="sm:col-span-2 lg:col-span-4">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Apply
        </button>
    </div>
</form>



    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Platform</th>
                    <th class="px-6 py-3 text-left font-semibold">Country</th>
                    <th class="px-6 py-3 text-right font-semibold">Streams</th>
                    <th class="px-6 py-3 text-right font-semibold">Earnings</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($stats as $row)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ $row->store }}</td>
                        <td class="px-6 py-4">{{ $row->country }}</td>
                        <td class="px-6 py-4 text-right">{{ number_format($row->total_streams) }}</td>
                        <td class="px-6 py-4 text-right">{{ formatCurrency($row->total_earnings, 'USD') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No data available for this month.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse ($stats as $row)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">

                <p class="text-sm text-gray-700">
                    Platform: <span class="font-medium">{{ $row->store }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Country: <span class="font-medium">{{ $row->country }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Streams:
                    <span class="font-medium">{{ number_format($row->total_streams) }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Earnings:
                    <span class="font-medium">{{ formatCurrency($row->total_earnings, 'USD') }}</span>
                </p>

            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No data available for this month.</p>
        @endforelse
    </div>

</div>
@endsection
