@extends('layouts.admin.app')
@section('title', 'Analytics Dashboard')
@section('content')
<div class="w-full max-w-3xl mx-auto px-4 py-8 space-y-6">


    {{-- Page Title --}}
    <div>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900">
            Analytics Dashboard
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Overview of your streaming performance.
        </p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Streams</p>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">
                {{ number_format($totalStreams) }}
            </h2>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-xs sm:text-sm mb-1">Top Track</p>
            <h2 class="text-sm sm:text-base font-semibold text-gray-900 truncate">
                {{ $topTracks->first()?->music?->title ?? 'N/A' }}
            </h2>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-xs sm:text-sm mb-1">Top Store</p>
            <h2 class="text-sm sm:text-base font-semibold text-gray-900 truncate">
                {{ $streamsByStore->first()?->store ?? 'N/A' }}
            </h2>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-xs sm:text-sm mb-1">Top Country</p>
            <h2 class="text-sm sm:text-base font-semibold text-gray-900 truncate">
                {{ $streamsByCountry->first()?->country ?? 'N/A' }}
            </h2>
        </div>

    </div>

    {{-- Monthly Trend Chart --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">
            Monthly Streams Trend
        </h2>
        <div id="monthlyTrendChart" class="w-full h-64 sm:h-80"></div>
    </div>

    {{-- Streams by Store --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">
            Streams by Store
        </h2>
        <div id="storeChart" class="w-full h-64 sm:h-80"></div>
    </div>

    {{-- Streams by Country --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">
            Top Countries
        </h2>
        <div id="countryChart" class="w-full h-64 sm:h-80"></div>
    </div>

</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const isMobile = window.innerWidth < 640;

    const monthlyLabels = @json($monthlyTrend->map(fn($m) => $m->month . '/' . $m->year));
    const monthlyData   = @json($monthlyTrend->pluck('total'));

    const chartBase = {
        chart: {
            height: isMobile ? 200 : 280,
            width: '100%',
            toolbar: { show: false },
            animations: { enabled: true },
        },
        grid: {
            padding: { left: isMobile ? 0 : 10, right: isMobile ? 0 : 10 }
        },
        stroke: { width: 2, curve: 'smooth' },
        dataLabels: { enabled: false },
        tooltip: { x: { show: true } },
    };

    // Monthly trend — area chart, works well vertically on mobile
    new ApexCharts(document.querySelector('#monthlyTrendChart'), {
        ...chartBase,
        chart: { ...chartBase.chart, type: 'area' },
        series: [{ name: 'Streams', data: monthlyData }],
        xaxis: {
            categories: monthlyLabels,
            labels: {
                rotate: isMobile ? -45 : 0,
                style: { fontSize: isMobile ? '10px' : '12px' },
            },
            tickAmount: isMobile ? 4 : undefined,
        },
        yaxis: {
            labels: { style: { fontSize: isMobile ? '10px' : '12px' } }
        },
    }).render();

    // Store chart — horizontal bars on mobile so store names aren't clipped
    new ApexCharts(document.querySelector('#storeChart'), {
        ...chartBase,
        chart: { ...chartBase.chart, type: 'bar' },
        plotOptions: {
            bar: {
                horizontal: isMobile,
                borderRadius: 4,
            }
        },
        series: [{ name: 'Streams', data: @json($streamsByStore->pluck('total')) }],
        xaxis: {
            categories: @json($streamsByStore->pluck('store')),
            labels: { style: { fontSize: isMobile ? '10px' : '12px' } },
        },
        yaxis: {
            labels: { style: { fontSize: isMobile ? '10px' : '12px' } }
        },
    }).render();

    // Country chart — same horizontal treatment on mobile
    new ApexCharts(document.querySelector('#countryChart'), {
        ...chartBase,
        chart: { ...chartBase.chart, type: 'bar' },
        plotOptions: {
            bar: {
                horizontal: isMobile,
                borderRadius: 4,
            }
        },
        series: [{ name: 'Streams', data: @json($streamsByCountry->pluck('total')) }],
        xaxis: {
            categories: @json($streamsByCountry->pluck('country')),
            labels: { style: { fontSize: isMobile ? '10px' : '12px' } },
        },
        yaxis: {
            labels: { style: { fontSize: isMobile ? '10px' : '12px' } }
        },
    }).render();
</script>
@endpush