@extends('layouts.user')
@section('title', 'Stats — ' . $project->title)

@section('content')
<div class="max-w-6xl mx-auto py-10 sm:px-6 space-y-6">

    {{-- Back --}}
    <a href="{{ route('user.stats') }}"
       class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-white transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        Back to Stats
    </a>

    {{-- Release Header --}}
    <div class="flex items-center gap-4 bg-zinc-900 border border-zinc-800 rounded-xl p-5">
       <div class="flex-shrink-0">
    @if ($project->cover_url)
        <img src="{{ $project->cover_url }}"
             class="w-20 h-20 rounded-xl object-cover"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="w-20 h-20 bg-zinc-700 rounded-xl hidden items-center justify-center">
            <img src="{{ asset('images/icons/music-fallback.svg') }}" class="w-8 h-8 opacity-70">
        </div>
    @else
        <div class="w-20 h-20 bg-zinc-700 rounded-xl flex items-center justify-center">
            <img src="{{ asset('images/icons/music-fallback.svg') }}" class="w-8 h-8 opacity-70">
        </div>
    @endif
</div>


        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-white truncate">{{ $project->title }}</h1>
            <p class="text-xs text-zinc-400 mt-1 uppercase tracking-wide">{{ $project->type }}</p>
        </div>

        <div class="text-right flex-shrink-0">
            <p class="text-3xl font-bold text-orange-500">{{ number_format($totalStreams) }}</p>
            <p class="text-xs text-zinc-500">Total Streams</p>
        </div>
    </div>

    {{-- Track List --}}
    <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl divide-y divide-zinc-800">
        @forelse ($tracks as $row)
            @php $music = $row->music; @endphp

            <div class="flex items-center justify-between p-4 hover:bg-zinc-900 transition">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="text-zinc-500 text-sm w-6 text-center">{{ $music->track_number }}</span>

                    <div class="min-w-0">
                        <p class="text-white font-medium truncate">{{ $music->title }}</p>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            {{ $row->store_count }} {{ Str::plural('store', $row->store_count) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-lg font-bold text-white">{{ number_format($row->total_streams) }}</p>
                        <p class="text-xs text-zinc-500">Streams</p>
                    </div>

                    <a href="{{ route('user.stats.show', $music->id) }}"
                       class="px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium transition">
                        Details
                    </a>
                </div>
            </div>

        @empty
            <div class="p-8 text-center text-zinc-500">
                No streaming data for this release.
            </div>
        @endforelse
    </div>

</div>
@endsection
