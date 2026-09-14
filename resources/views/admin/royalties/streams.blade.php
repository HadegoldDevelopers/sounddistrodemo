@extends('layouts.admin.app')

@section('title', 'Streams Report')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Page Header -->
    <div>
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Streams Report</h2>
        <p class="text-sm text-gray-600 mt-1">
            Monthly streaming performance by artist and track.
        </p>
    </div>

    <!-- Filters -->
    <form method="GET"
          class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Artist -->
       <select name="artist_id" class="p-2 border rounded w-full">
    <option value="" {{ request('artist_id') ? '' : 'selected' }}>All Artists</option>
    @foreach ($artists as $artist)
        <option value="{{ $artist->artist_id }}"
            {{ request('artist_id') && request('artist_id') == $artist->artist_id ? 'selected' : '' }}>
            {{ $artist->artist }}
        </option>
    @endforeach
</select>



        <!-- Year -->
        <select name="year" class="p-2 border rounded w-full">
            <option value="">All Years</option>
            @for ($y = now()->year; $y >= 2015; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>

        <!-- Month -->
        <select name="month" class="p-2 border rounded w-full">
    <option value="">All Months</option>
    @foreach ($months as $num => $name)
        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
            {{ $name }}
        </option>
    @endforeach
</select>


        <!-- Search -->
        <input type="text"
               name="song"
               placeholder="Search song title"
               value="{{ request('song') }}"
               class="p-2 border rounded w-full">

        <!-- Buttons -->
        <div class="flex gap-3 sm:col-span-2 lg:col-span-4">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Apply
            </button>

            <a href="{{ route('admin.royalties.reports.export.artists.monthly', [
                    'artist_id' => request('artist_id'),
                    'year' => request('year'),
                    'month' => request('month')
                ]) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Export CSV
            </a>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Streams</p>
            <p class="text-2xl font-bold text-blue-600">
                {{ number_format($summary['total_streams'] ?? 0) }}
            </p>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Tracks</p>
            <p class="text-2xl font-bold text-indigo-600">
                {{ $summary['track_count'] ?? 0 }}
            </p>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Periods</p>
            <p class="text-2xl font-bold text-rose-600">
                {{ $summary['periods'] ?? 0 }}
            </p>
        </div>

    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Artist</th>
                    <th class="px-6 py-3 text-left font-semibold">Track</th>
                    <th class="px-6 py-3 text-left font-semibold">Period</th>
                    <th class="px-6 py-3 text-right font-semibold">Streams</th>
                    <th class="px-6 py-3 text-center font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($stats as $row)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ $row->artist }}</td>
                        <td class="px-6 py-4">{{ $row->title }}</td>
                        <td class="px-6 py-4">
                            {{ $row->period }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            {{ number_format($row->total_streams) }}
                        </td>
                       <td class="px-6 py-4 text-center flex items-center justify-center gap-3">

    <!-- View Report -->
    <a href="{{ route('admin.royalties.reports.streams.detail', [
            'music_id' => $row->music_id,
            'year' => $row->year,
            'month' => $row->month
        ]) }}"
       class="text-indigo-600 hover:underline font-medium">
        View Report
    </a>
    <!-- Export CSV -->
    <a href="{{ route('admin.royalties.reports.export.artists.monthly', [
            'artist_id' => $row->artist_id,
            'year' => $row->year,
            'month' => $row->month
        ]) }}"
       class="text-green-600 hover:underline font-medium">
        Export CSV
    </a>
{{-- Send Report --}}
<form method="POST"
      action="{{ route('admin.royalties.reports.send') }}"
      class="inline"
      onsubmit="return confirm('Send earnings report to {{ addslashes($row->artist) }}?')">
    @csrf
    <input type="hidden" name="artist_id" value="{{ $row->artist_id }}">
    <input type="hidden" name="year"      value="{{ $row->year }}">
    <input type="hidden" name="month"     value="{{ $row->month }}">
    <button type="submit"
            class="text-purple-600 hover:underline font-medium cursor-pointer">
        Send Report
    </button>
</form>
</td>

</tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No stream data found.
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

                <h3 class="font-semibold text-lg text-gray-900">{{ $row->title }}</h3>

                <p class="text-sm text-gray-700">
                    Artist: <span class="font-medium">{{ $row->artist }}</span>
                </p>

                <p class="text-sm text-gray-700">
                    Period:
                    <span class="font-medium">
                         {{ $row->period }}
                    </span>
                </p>

                <p class="text-sm text-gray-700">
                    Streams:
                    <span class="font-medium">{{ number_format($row->total_streams) }}</span>
                </p>

               <div class="flex gap-3 pt-2">

    <a href="{{ route('admin.royalties.reports.streams.detail', [
            'music_id' => $row->music_id,
            'year' => $row->year,
            'month' => $row->month
        ]) }}"
       class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
        View Report
    </a>

    <a href="{{ route('admin.royalties.reports.export.artists.monthly', [
            'artist_id' => $row->artist_id,
            'year' => $row->year,
            'month' => $row->month
        ]) }}"
       class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
        Export CSV
    </a>
{{-- Send Report --}}
<form method="POST"
      action="{{ route('admin.royalties.reports.send') }}"
      class="inline"
      onsubmit="return confirm('Send earnings report to {{ addslashes($row->artist) }}?')">
    @csrf
    <input type="hidden" name="artist_id" value="{{ $row->artist_id }}">
    <input type="hidden" name="year"      value="{{ $row->year }}">
    <input type="hidden" name="month"     value="{{ $row->month }}">
    <button type="submit"
            class="text-purple-600 hover:underline font-medium cursor-pointer">
        Send Report
    </button>
</form>
</div>


            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No stream data found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $stats, '__pager' => $stats])
    </div>

</div>
@endsection
