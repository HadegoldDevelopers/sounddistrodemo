@extends('layouts.admin.app')

@section('title', 'Edit Metadata')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">
    <h1 class="text-2xl font-semibold mb-6">
        Edit Metadata: {{ $project->title }}
    </h1>

    <form action="{{ route('admin.releases.metadata.update', $project->id) }}" 
          method="POST" 
          class="space-y-8 bg-white p-6 rounded shadow">
        @csrf

        {{-- ===================== --}}
        {{-- PROJECT METADATA --}}
        {{-- ===================== --}}
        <div class="border-b pb-6">
            <h2 class="text-lg font-semibold mb-4">Project Information</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text"
                           name="title"
                           value="{{ old('title', $project->title) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Display Artist</label>
                    <input type="text"
                           name="artist_display"
                           value="{{ old('artist_display', $project->artist_display) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Genre</label>
                    <input type="text"
                           name="genre"
                           value="{{ old('genre', $project->genre) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Release Date</label>
                    <input type="date"
                           name="release_date"
                           value="{{ old('release_date', optional($project->release_date)->format('Y-m-d')) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium">UPC</label>
                    <input type="text"
                           name="upc"
                           value="{{ old('upc', $project->upc) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

            </div>
        </div>


        {{-- ===================== --}}
        {{-- TRACK METADATA --}}
        {{-- ===================== --}}
        <div>
            <h2 class="text-lg font-semibold mb-4">Tracks</h2>

            @foreach($project->tracks as $index => $track)
                <div class="mb-6 p-4 border rounded bg-gray-50">

                    <h3 class="font-semibold mb-3">
                        Track {{ $index + 1 }}: {{ $track->title }}
                    </h3>

                    <input type="hidden" name="tracks[{{ $index }}][id]" value="{{ $track->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium">Artist</label>
                            <input type="text"
                                   name="tracks[{{ $index }}][artist]"
                                   value="{{ old("tracks.$index.artist", $track->artist) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Featured Artist(s)</label>
                            <input type="text"
                                   name="tracks[{{ $index }}][featured_artists]"
                                   value="{{ old("tracks.$index.featured_artists", $track->featured_artists) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">ISRC</label>
                            <input type="text"
                                   name="tracks[{{ $index }}][isrc]"
                                   value="{{ old("tracks.$index.isrc", $track->isrc) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Credits / Producer</label>
                            <input type="text"
                                   name="tracks[{{ $index }}][credits]"
                                   value="{{ old("tracks.$index.credits", $track->credits) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <div>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Save Changes
            </button>

            <a href="{{ route('admin.releases.all') }}"
               class="ml-4 text-gray-600 hover:underline">
               Cancel
            </a>
        </div>

    </form>
</div>
@endsection    