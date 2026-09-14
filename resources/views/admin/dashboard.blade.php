@extends('layouts.admin.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-gray-100 p-6">

    {{-- Page Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-gray-500 text-sm">Overview of your music distribution platform</p>
        </div>
        <a href="{{route('admin.royalties.reports.streams')}}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow">
            Download Reports 
        </a>
    </div>

    {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-10">

    {{-- New Signups --}}
    <div class="min-w-0 text-white p-6 rounded-xl shadow-lg"
     style="background-color: #78350f; border: 1px solid rgba(120, 53, 15, 0.3);">
    <p class="uppercase text-sm opacity-80">New Signups</p>
    <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">
        {{ $newSignups }}
    </h2>
</div>
    {{-- Signups Last 7 Days --}}
    <div class="min-w-0 text-white p-6 rounded-xl shadow-lg"
     style="background-color: #1e293b; border: 1px solid rgba(100, 116, 139, 0.3);">
    <p class="uppercase text-sm opacity-80">Signups (Last 7 Days)</p>
    <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">
        {{ $last7DaysSignups}}
    </h2>
</div>
    {{-- Total Users --}}
    <div class="min-w-0 bg-blue-600 text-white p-6 rounded-xl shadow-lg border border-blue-700/30">
        <p class="uppercase text-sm opacity-80">Total Users</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">{{ $totalUsers }}</h2>
    </div>

    {{-- Total DSP Revenue --}}
    <div class="min-w-0 bg-green-600 text-white p-6 rounded-xl shadow-lg border border-yellow-600/30">
        <p class="uppercase text-sm opacity-80">Total DSP Revenue</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">
            {{ formatCurrency($totalRevenue, 'USD') }}
        </h2>
    </div>
{{-- Platform Revenue --}}
    <div class="min-w-0 bg-teal-600 text-white p-6 rounded-xl shadow-lg border border-teal-700/30">
        <p class="uppercase text-sm opacity-80">Platform Revenue</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">{{ formatCurrency($platformRevenue, 'USD') }}
</h2>
    </div>
 {{-- Active Subscribers --}}
    <div class="min-w-0 bg-purple-600 text-white p-6 rounded-xl shadow-lg border border-purple-700/30">
        <p class="uppercase text-sm opacity-80">Active Subscribers</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">{{ $activeSubscribers }}</h2>
    </div>
    
    

    {{-- Pending Earnings --}}
    <div class="min-w-0 bg-orange-500 text-white p-6 rounded-xl shadow-lg border border-orange-600/30">
        <p class="uppercase text-sm opacity-80">Pending Earnings</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">{{ formatCurrency($pendingEarnings, 'USD') }}</h2>
    </div>

    {{-- Total Paid Out --}}
    <div class="min-w-0 bg-indigo-600 text-white p-6 rounded-xl shadow-lg border border-indigo-700/30">
        <p class="uppercase text-sm opacity-80">Total Paid Out</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">{{ formatCurrency($totalPaidOut, 'USD') }}</h2>
    </div>

    {{-- Pending Withdrawals --}}
    <div class="min-w-0 bg-red-600 text-white p-6 rounded-xl shadow-lg border border-red-700/30">
        <p class="uppercase text-sm opacity-80">Pending Withdrawals</p>
        <h2 class="text-[clamp(1.5rem,2vw+1rem,2.5rem)] font-bold mt-2 break-words">
           {{ formatCurrency($pendingWithdrawals, 'USD') }} </h2>
    </div>
</div>
  


    {{-- Main Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Chart --}}
        <div class="bg-white p-6 rounded-xl shadow col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Monthly Album Uploads</h2>
                <span class="text-sm text-gray-500">Last 12 months</span>
            </div>
           <div id="albumChart" class="w-full h-64"></div>
        </div>

        {{-- Quick Stats --}}
        <div class="space-y-6">

            <div class="bg-white p-6 rounded-xl shadow">
                <p class="text-sm text-gray-500">Pending Releases</p>
                <h3 class="text-3xl font-bold text-gray-800">
                  {{$pendingApprovals}}  
                </h3>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <p class="text-sm text-gray-500">Releases this month</p>
                <h3 class="text-3xl font-bold text-red-600">
                 {{ $releasesThisMonth }}   
                </h3>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <p class="text-sm text-gray-500">Total Releases</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ $totalReleases }}</h3>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
  const albumOptions = {
    chart: {
      type: 'area',
      height: 250,
      toolbar: { show: false }
    },
    series: [{
      name: 'Uploads',
      data: @json(array_values($albumUploads))
    }],
    xaxis: {
      categories: @json(array_keys($albumUploads)),
      labels: { style: { colors: '#6B7280' } }
    },
    colors: ['#8b5cf6'],
    fill: { type: 'gradient' },
    stroke: { curve: 'smooth', width: 3 }
  };

  new ApexCharts(document.querySelector("#albumChart"), albumOptions).render();
</script>
@endpush
