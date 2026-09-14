@extends('layouts.user')
@section('title', 'Streaming Stats — ' . ($music->title ?? 'Track'))

@section('content')
<div class="max-w-6xl mx-auto py-10 sm:px-6">
    {{-- Back and title --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('user.stats') }}"
           class="w-9 h-9 flex items-center justify-center bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-lg transition flex-shrink-0"
           aria-label="Back to stats">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div class="min-w-0">
            <h1 class="text-lg sm:text-2xl font-bold text-white truncate">Streaming Stats</h1>
            <p class="text-xs sm:text-sm text-zinc-400 mt-0.5 truncate">{{ $music->title ?? '' }}</p>
        </div>
    </div>

    {{-- Track Info Card --}}
    <div class="flex items-center gap-3 bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 mb-4">
<div class="flex-shrink-0">
    @if ($music->cover_url)
        <img src="{{ $music->cover_url }}"
             alt="{{ $music->title ?? 'Cover' }}"
             loading="lazy"
             class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl object-cover"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl bg-zinc-700 hidden items-center justify-center">
            <img src="{{ asset('images/icons/music-fallback.svg') }}"
                 class="w-6 h-6 opacity-70"
                 alt="No Cover">
        </div>
    @else
        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl bg-zinc-700 flex items-center justify-center">
            <img src="{{ asset('images/icons/music-fallback.svg') }}"
                 class="w-6 h-6 opacity-70"
                 alt="No Cover">
        </div>
    @endif
</div>


    <div class="flex-1 min-w-0">
        <h2 class="font-semibold text-white truncate text-sm sm:text-lg">{{ $music->title ?? 'Unknown Track' }}</h2>
       <p class="text-xs sm:text-sm text-zinc-400">{{ $music->artist_display }}</p>

    </div>

    <div class="text-right flex-shrink-0">
        <p class="text-xl sm:text-3xl font-bold text-orange-500">{{ number_format($totalStreams) }}</p>
        <p class="text-xs text-zinc-500 mt-0.5">Total Streams</p>
    </div>

</div>


    {{-- Filters responsive --}}
    <form method="GET" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <div>
                <label class="block text-xs text-zinc-500 mb-1 uppercase tracking-wider">Date Range</label>
                <select name="range"
                        class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 rounded-lg px-3 py-2 text-sm">
                    <option value=""    {{ !request('range') ? 'selected' : '' }}>All Time</option>
                    <option value="7d"  {{ request('range') === '7d'  ? 'selected' : '' }}>Last 7 Days</option>
<option value="14d" {{ request('range') === '14d' ? 'selected' : '' }}>Last 14 Days</option>
<option value="30d" {{ request('range') === '30d' ? 'selected' : '' }}>Last 30 Days</option>
<option value="90d" {{ request('range') === '90d' ? 'selected' : '' }}>Last 90 Days</option>

                    <option value="1m"  {{ request('range') === '1m'  ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="3m"  {{ request('range') === '3m'  ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6m"  {{ request('range') === '6m'  ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="12m" {{ request('range') === '12m' ? 'selected' : '' }}>Last 12 Months</option>
                    <option value="ytd" {{ request('range') === 'ytd' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-zinc-500 mb-1 uppercase tracking-wider">Store</label>
                <select name="store"
                        class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Stores</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store }}" {{ request('store') === $store ? 'selected' : '' }}>
                            {{ $store }}
                        </option>
                    @endforeach
                </select>
            </div>

          {{-- Apply / Clear (responsive sizes) --}}
<div class="flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-3">
    <button type="submit"
            class="w-full sm:w-auto px-3 py-2 sm:px-4 sm:py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition">
        Apply
    </button>

    <a href="{{ route('user.stats.show', $music->id) }}"
       class="w-full sm:w-auto px-3 py-2 sm:px-4 sm:py-2 bg-zinc-700 text-zinc-300 rounded-lg text-sm hover:bg-zinc-600 transition text-center">
        Clear
    </a>
</div>

        </div>

        @if (request('range'))
        <p class="text-xs text-zinc-500 mt-3">Showing: <span class="text-zinc-300">{{ $periodLabel ?? 'Selected range' }}</span></p>
        @endif
    </form>

    {{-- Store breakdown and chart --}}
    @if ($storeBreakdown->isNotEmpty())
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 sm:p-6 mb-4">
        <h2 class="text-sm sm:text-base font-semibold text-white mb-4">Streams by Store</h2>

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Store list --}}
            <div class="flex-1 divide-y divide-zinc-800">

                @foreach ($storeBreakdown as $store => $streams)

                    @php
                        // Load full config from config/stores.php
                        $storeConfig = config('stores');

                        // Use config if store exists, otherwise fallback
                        $cfg = $storeConfig[$store] ?? [
                            'icon'  => 'default.svg',
                            'color' => '#6366f1',
                        ];

                        // Auto-generate transparent background
                        $bg = $cfg['color'] . '20';
                    @endphp

                    <div class="flex items-center justify-between py-3">

                        <div class="flex items-center gap-3">

                            {{-- Icon --}}
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: {{ $bg }};">
                                <img src="{{ asset('images/icons/' . $cfg['icon']) }}"
                                     class="h-6 md:h-7 object-contain
                 hover:opacity-100 transition"
                                     alt="{{ $store }}"
                                     loading="lazy">
                            </div>

                            {{-- Store name --}}
                            <span class="text-sm font-medium text-zinc-200 truncate">{{ $store }}</span>
                        </div>

                        {{-- Streams --}}
                        <div class="text-right ml-3">
                            <p class="text-xs text-zinc-500">Streams</p>
                            <p class="text-base font-bold text-white">{{ number_format($streams) }}</p>
                        </div>

                    </div>

                @endforeach

            </div>

            {{-- Chart container --}}
            @if ($monthlyTrend->isNotEmpty())
                <div class="lg:w-96 w-full">
                    <div id="mainChart" class="w-full" aria-hidden="true"></div>
                </div>
            @endif

        </div>
    </div>
@endif


    {{-- Top countries --}}
   @if ($countryBreakdown->count())
<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 sm:p-6 mb-4">
    <h2 class="text-sm sm:text-base font-semibold text-white mb-4">Top Countries</h2>

    @php
        $maxCountry = $countryBreakdown->max('streams') ?: 0;
    @endphp

    <div class="space-y-3">
        @foreach ($countryBreakdown as $item)
            <div class="flex items-center gap-3">
                <span class="text-sm text-zinc-300 w-28 sm:w-32 flex-shrink-0 truncate">
                    {{ $item->country }}
                </span>

                <div class="flex-1 bg-zinc-800 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full bg-orange-500 transition-all"
                         style="width: {{ $maxCountry ? ($item->streams / $maxCountry) * 100 : 0 }}%">
                    </div>
                </div>

                <span class="text-sm font-semibold text-white w-16 text-right flex-shrink-0">
                    {{ number_format($item->streams) }}
                </span>
            </div>
        @endforeach
    </div>

    @if($countryBreakdown->hasPages())
        <div class="mt-4">
            <x-pager :__pager="$countryBreakdown" />
        </div>
    @endif
</div>
@endif


    @if ($storeBreakdown->isEmpty())
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 text-center">
    <div class="flex justify-center mb-3">
        <img src="{{ asset('images/icons/stats-empty.svg') }}"
             alt="No Data"
             class="h-10 w-10 opacity-70">
    </div>

    <p class="text-zinc-300 font-semibold text-lg">No streaming data yet</p>
    <p class="text-sm text-zinc-500 mt-2">
        Data will appear here once your release starts getting streams from stores.
    </p>
</div>

    @endif
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
@if ($monthlyTrend->isNotEmpty())
new ApexCharts(document.querySelector('#mainChart'), {
    chart: {
        type: 'area',
        height: 260,
        toolbar: { show: false },
        background: 'transparent',
    },
    theme: { mode: 'dark' },
    series: [{
        name: 'Streams',
        data: @json($monthlyTrend->values()),
    }],
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.02,
        }
    },
    dataLabels: { enabled: false },
    grid: {
        borderColor: '#27272a',
        strokeDashArray: 4,
    },
    xaxis: {
        categories: @json($monthlyTrend->keys()->map(fn($k) => \Carbon\Carbon::createFromFormat('Y-m', $k)->format('M Y'))),
        labels: { style: { fontSize: '10px', colors: '#71717a' } },
        axisBorder: { show: false },
        axisTicks: { show: false },
        tickAmount: 5,
    },
    yaxis: { labels: { style: { colors: '#71717a', fontSize: '11px' } } },
    tooltip: {
        theme: 'dark',
        y: { formatter: val => val.toLocaleString() + ' streams' },
    },
    colors: ['#f97316'],
}).render();
@endif
</script>
@endpush