@extends('layouts.user')
@section('title', 'Streaming Stats')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-8">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-white">Streaming Stats</h1>
        <p class="text-sm text-zinc-400 mt-1">Overview of your releases streaming performance.</p>
    </div>

    {{-- Total Streams Banner --}}
    <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-5 flex items-center justify-between">
        <p class="text-sm text-zinc-400">Total Streams Across All Releases</p>
        <p class="text-3xl font-bold text-orange-500">{{ number_format($totalStreams) }}</p>
    </div>

    @if ($projects->isNotEmpty())

        <div class="space-y-4">
            @foreach ($projects as $row)
                @php $project = $row->project; @endphp
                @if (!$project) @continue @endif

                <div class="group bg-zinc-900/60 border border-zinc-800 rounded-xl p-5
                            flex flex-col sm:flex-row sm:items-center justify-between gap-4
                            hover:bg-zinc-900 hover:border-zinc-700 transition duration-300">

                    {{-- Left: Cover + Info --}}
                    <div class="flex items-center gap-4 min-w-0">

                        {{-- Cover --}}
                        <div class="flex-shrink-0">
                            @if ($project->cover_url)
    <img src="{{ $project->cover_url }}"
         class="w-16 h-16 rounded-lg object-cover shadow-md"
         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <div class="w-16 h-16 bg-zinc-700 rounded-lg hidden items-center justify-center">
        <img src="{{ asset('images/icons/music-fallback.svg') }}" class="w-6 h-6 opacity-70">
    </div>
@else
    <div class="w-16 h-16 bg-zinc-700 rounded-lg flex items-center justify-center">
        <img src="{{ asset('images/icons/music-fallback.svg') }}" class="w-6 h-6 opacity-70">
    </div>
@endif

                        </div>

                        {{-- Info --}}
                        <div class="min-w-0">
                            <h3 class="text-white font-semibold text-lg truncate">{{ $project->title }}</h3>

                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-zinc-700 text-zinc-300 text-[11px] uppercase tracking-wide">
                                {{ $project->type }}
                            </span>

                            <p class="text-xs text-zinc-500 mt-2">
                                {{ $row->track_count }} {{ Str::plural('track', $row->track_count) }}
                                &nbsp;·&nbsp;
                                {{ $row->store_count }} {{ Str::plural('store', $row->store_count) }}
                            </p>
                        </div>

                    </div>

                    {{-- Right: Streams + Button --}}
                    <div class="flex items-center gap-6 flex-shrink-0">

                        <div class="text-right">
                            <p class="text-xl font-bold text-white">{{ number_format($row->total_streams) }}</p>
                            <p class="text-xs text-zinc-500">Streams</p>
                        </div>

                        <a href="{{ route('user.stats.release', $project->id) }}"
                           class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium text-sm
                                  shadow-md transition duration-300 hover:shadow-orange-500/20 whitespace-nowrap">
                            View Stats
                        </a>

                    </div>

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($projects->hasPages())
            <div class="pt-6 flex justify-center">
              <x-pager :__pager="$projects" />
            </div>
        @endif

    @else

        {{-- Empty State --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-16 text-center">
            <img src="{{ asset('images/icons/stats-empty.svg') }}"
                 alt="No Data"
                 class="h-12 w-12 opacity-70 mx-auto mb-4">

            <p class="text-zinc-300 font-semibold text-lg">No streaming data yet</p>
            <p class="text-sm text-zinc-500 mt-2">
                Data will appear here once your releases go live on stores.
            </p>
        </div>

    @endif

</div>
@endsection
