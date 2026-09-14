@extends('layouts.user')

@section('title', 'My Releases')

@section('content')
<div class="max-w-6xl mx-auto py-10 sm:px-6">
        @if($releases->count())

            <div class="grid gap-5">
                @foreach($releases as $release)
                    <div class="group bg-zinc-900/60 backdrop-blur-sm border border-zinc-800 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between 
                                hover:bg-zinc-900 hover:border-zinc-700 transition duration-300 shadow-sm">

                        {{-- Left Side: Cover + Info --}}
                        <div class="flex items-center space-x-4 mb-3 sm:mb-0 w-full sm:w-auto">

                            @if($release->cover_url)
    <img src="{{ $release->cover_url }}" class="w-16 h-16 rounded-lg object-cover shadow-md" />
@else
    <div class="w-16 h-16 bg-zinc-700 rounded-lg"></div>
@endif


                            <div>
                                <h3 class="text-white font-semibold text-lg leading-tight">
                                    {{ $release->title }}
                                </h3>
                                <p class="text-xs text-zinc-400 mt-1">
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-zinc-700 text-zinc-300 text-[11px] uppercase tracking-wide">
                                        {{ strtoupper($release->type) }}
                                    </span>
                                </p>

                                @if(in_array(strtolower($release->type), ['album', 'ep']))
                                    <p class="text-sm text-zinc-400">
                                        <span class="font-medium text-zinc-300">Tracks:</span> {{ $release->tracks->count() }}
                                    </p>
                                @endif

                                @php
                                    $artistName = $release->tracks->first()->artist ?? 'Unknown Artist';
                                @endphp

                                <p class="text-sm text-zinc-400 mt-1">
                                    <span class="font-medium text-zinc-300">Artist:</span> {{ $artistName }} <br>
                                    <span class="font-medium text-zinc-300">Released:</span>
                                    {{ $release->release_date->format('M d, Y') }}
                                </p>

                            </div>
                        </div>

                        {{-- Right Side: Status + View --}}
                        <div class="flex items-center space-x-3 w-full sm:w-auto justify-start sm:justify-end">

                            {{-- Status Badge --}}
                            @php
                                $status = strtolower($release->status);

                                $statusColors = [
                                    'draft' => 'bg-zinc-700 text-zinc-300',
                                    'approved' => 'bg-green-500/20 text-green-400',
                                    'pending' => 'bg-yellow-500/20 text-yellow-400',
                                    'rejected' => 'bg-red-500/20 text-red-400',
                                ];

                                $badgeClass = $statusColors[$status] ?? 'bg-zinc-700 text-zinc-300';
                            @endphp
{{-- Show text only if approved --}}
 @if($status === 'approved')
        {{-- Info icon --}}
        <button type="button"
        data-tooltip="tooltip-{{ $release->id }}"
        class="ml-1 text-zinc-400 hover:text-zinc-200 focus:outline-none"
        onclick="document.getElementById('tooltip-{{ $release->id }}').classList.toggle('hidden')">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>
        </button>

        {{-- Tooltip (hidden by default) --}}
        <div id="tooltip-{{ $release->id }}"
             class="hidden absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-48 bg-zinc-800 text-zinc-300 text-xs rounded px-2 py-1 shadow-lg z-50">
            We partnered with Symphonic to manage our YouTube content distribution.
        </div> 
    @endif
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ ucfirst($status) }}
                            </span>

                            {{-- View Button --}}
                            <button onclick="openReleaseModal({{ $release->id }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm 
                       shadow-md transition duration-300 hover:shadow-blue-500/20 text-center">
                                View
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($releases->hasPages())
    <div class="mt-4">
        <x-pager :__pager="$releases" />
    </div>
@endif
        @else
            <p class="text-gray-400">No releases found.</p>
        @endif

    </div>
    <div id="releaseModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50">

        <div class="bg-zinc-900 w-full max-w-xl rounded-xl p-6 border border-zinc-700 shadow-xl relative">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white">Release Details</h2>

                <button onclick="closeReleaseModal()" class="w-8 h-8 flex items-center justify-center 
                       bg-zinc-800 hover:bg-zinc-700 text-white rounded-full text-xl font-bold shadow-md">
                    &times;
                </button>
            </div>

            <div id="modalContent" class="text-zinc-300 text-sm space-y-3">
                <!-- AJAX content goes here -->
            </div>

        </div>
    </div>


@endsection
@push('scripts')
    <script>
        function openReleaseModal(id) {
            const modal = document.getElementById('releaseModal');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            content.innerHTML = "<p class='text-zinc-400'>Loading...</p>";

            fetch(`/releases/${id}`)
                .then(res => res.json())
                .then(data => {
                    let html = `
                    <div class="flex items-center space-x-4">
                        <img src="${data.cover}" class="w-20 h-20 rounded-lg object-cover shadow-md">
                        <div>
                            <p class="text-lg font-semibold text-white">${data.title}</p> 
                            <p class="text-sm text-zinc-300 mt-1"><strong>
                            UPC:</strong> ${data.upc ?? 'waiting for approval'}</p>
                            <p class="text-xs text-zinc-400 mt-1">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-700 text-zinc-300 text-[11px] uppercase tracking-wide">
                                    ${data.type}
                                </span>
                            </p>
                            <p class="text-sm text-zinc-300 mt-1"><strong>Artist:</strong> ${data.artist}</p>
                        </div>
                    </div>


                    <p><strong>Release Date:</strong> ${data.release_date}</p>
                    <p><strong>Genre:</strong>  ${data.genre} (${data.subgenre ?? 'No Sub-genre'})</p>
                    <p><strong>Label:</strong> ${data.label}</p>
                    <p><strong>Explicit:</strong> ${data.explicit ? 'Yes' : 'No'}</p>
                `;

                    if (data.tracks.length > 0) {
                        html += `<div><strong>Tracks:</strong><ul class="mt-2 space-y-1">`;
                        data.tracks.forEach(t => {
                            let feat = t.featured_artists ? ` feat. ${t.featured_artists}` : '';
                            html += `
                <li class="text-zinc-300">
                    #${t.track_number} — ${t.title} (${t.artist}${feat})
                    <div class="text-xs text-zinc-400">
                    ISRC: ${t.isrc ?? 'waiting for approval'}
                    </div>
                    <div class="text-xs text-zinc-400">
                        Producer(s): ${t.credits ?? 'N/A'}
                    </div>
                </li>
            `;
                        });
                        html += `</ul></div>`;
                    }


                    content.innerHTML = html;
                });
        }

        function closeReleaseModal() {
            document.getElementById('releaseModal').classList.add('hidden');
        }
        document.getElementById('releaseModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeReleaseModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === "Escape") {
                closeReleaseModal();
            }
        });
    </script>
<script>
document.addEventListener("click", function (e) {
    const tooltips = document.querySelectorAll("[id^='tooltip-']");

    tooltips.forEach(tip => {
        const button = document.querySelector(`[data-tooltip='${tip.id}']`);

        // If clicking outside both the button and tooltip → hide it
        if (button && !button.contains(e.target) && !tip.contains(e.target)) {
            tip.classList.add("hidden");
        }
    });
});
</script>

@endpush