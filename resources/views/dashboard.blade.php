@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

        @if(auth()->user()->role === 'label')
            <!-- Label Dashboard -->

            <!-- Overview Cards -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-300">Total Releases</h2>
                        <i class="fas fa-cloud-upload-alt text-orange-500 text-2xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white">{{ $submittedSongs }}</p>
                    <p class="text-sm text-gray-400 mt-2">
                        @if ($percentChange > 0)
                            <span class="text-green-400">+{{ $percentChange }}%</span> from last month
                        @elseif($percentChange < 0)
                            <span class="text-red-400">{{ $percentChange }}%</span> from last month
                        @else
                            No change from last month
                        @endif
                    </p>
                </div>

                <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-300">Total Streams</h2>
                        <i class="fas fa-play-circle text-blue-500 text-2xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white">
                        {{ number_format($totalStreams) }}
                    </p>
                    <p class="text-sm text-gray-400 mt-2">Last 30 days</p>
                </div>

                <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-300">Earnings (Est.)</h2>
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white">{{ $totalEarnings }}</p>
                    <p class="text-sm text-gray-400 mt-2">Last 30 days</p>
                </div>
                <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-300">Active Artists</h2>
                        <i class="fas fa-users-line text-purple-500 text-2xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white">{{ $totalArtists }}</p>
                    <p class="text-sm text-gray-400 mt-2">Currently distributing</p>
                </div>
            </section>

     @elseif(auth()->user()->role === 'artist')
        <!-- Artist Dashboard -->

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-300">Your Releases</h2>
                    <i class="fas fa-cloud-upload-alt text-orange-500 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-white">{{ $submittedSongs }}</p>
            </div>

            <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-300">Your Streams</h2>
                    <i class="fas fa-play-circle text-blue-500 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-white">{{ number_format($totalStreams) }}</p>
                 <p class="text-sm text-gray-400 mt-2">Last 30 days</p>
            </div>

            <div class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-300">Earnings (Est.)</h2>
                    <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                </div>
                <p class="text-4xl font-bold text-white">{{ $totalEarnings }}</p>
                <p class="text-sm text-gray-400 mt-2">Last 30 days</p>
            </div>
        </section>
     @else
            <p class="text-white">Role not recognized.</p>
        @endif

    <!-- Recent Releases Cards -->
    <section class="bg-zinc-800 rounded-lg p-4 sm:p-6 shadow-xl">
      <h2 class="text-xl font-semibold text-gray-300 mb-4">Recent Releases</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($projects as $project)
      <div class="bg-zinc-700 rounded-lg p-4 flex items-center space-x-4 hover:bg-zinc-600 transition">

        <!-- Cover -->
        <div class="flex-shrink-0">
          <img src="{{ assetPath($project->cover_path) }}"
               class="w-16 h-16 rounded-md object-cover"
               alt="Cover">
        </div>

        <!-- Info -->
        <div class="flex-1">
          <h3 class="text-white font-semibold text-lg truncate">
            {{ $project->title }}
          </h3>

          <!-- Type: Single / EP / Album -->
          <p class="text-orange-400 text-xs font-semibold mt-1">
            {{ $project->type }}
          </p>

          <!-- Genre -->
          <p class="text-gray-400 text-sm truncate">
            {{ $project->genre ?? 'Unknown' }}
          </p>

          <div class="flex items-center justify-between mt-2">

            <!-- Status -->
            @php
        $statusColors = [
            'approved' => 'bg-green-500/20 text-green-400',
            'pending' => 'bg-yellow-500/20 text-yellow-400',
            'rejected' => 'bg-red-500/20 text-red-400',
        ];
        $status = strtolower($project->status ?? 'pending');
        $classes = $statusColors[$status] ?? 'bg-gray-500/20 text-gray-400';
            @endphp

            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $classes }}">
              {{ ucfirst($status) }}
            </span>

            <!-- Release Date -->
            <span class="text-gray-400 text-xs">
              {{ $project->release_date->format('M d, Y') }}
            </span>
          </div>
        </div>
      </div>
    @empty
      <p class="text-gray-400 col-span-full text-center">No Releases found.</p>
    @endforelse
      </div>
    </section>

    <div class="mt-6 bg-white/70 backdrop-blur-sm p-6 rounded-2xl shadow-sm flex flex-wrap gap-4">

       <div class="mt-6 flex flex-wrap gap-4">

        {{-- New Release --}}
        <a href="{{ route('music.upload') }}"
           class="group flex items-center gap-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <i class="fas fa-plus-circle text-xl group-hover:scale-110 transition-transform"></i>
            <span>{{ __('New Release') }}</span>
        </a>

        {{-- Support --}}
        <a href="mailto:{{ $global['email']}}"
            class="group flex items-center gap-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <i class="fas fa-headset text-xl group-hover:scale-110 transition-transform"></i>
            <span>Support</span>
        </a>

        {{-- Manage Artists (Label Only) --}}
        @if(auth()->user()->role === 'label')
            <a href="{{ route('artists.index') }}"
               class="group flex items-center gap-3 bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <i class="fas fa-users-gear text-xl group-hover:scale-110 transition-transform"></i>
                <span>{{ __('Manage Artists') }}</span>
            </a>
        @endif

    </div>
    </div>
@endsection