@extends('layouts.admin.app')

@section('title', 'Release Details')

@section('content')

<div class="max-w-4xl mx-auto p-6 bg-white shadow rounded-lg space-y-6">
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
        <h1 class="text-2xl font-bold">{{ $release->title }}</h1>
        
        <div class="flex space-x-2 mt-2 sm:mt-0">
            <a href="{{ route('admin.releases.metadata', $release->id) }}"
               class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Edit</a>

            @if($release->status !== 'approved')
            <form action="{{ route('admin.releases.approved', $release->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Approve</button>
            </form>
            @endif

            @if($release->status !== 'rejected')
            <form action="{{ route('admin.releases.rejected', $release->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Reject</button>
            </form>
            @endif
        </div>
    </div>
    
    <div class="space-y-2 text-gray-700">
        <p><strong>Artist:</strong> {{ $release->artist_display }}</p>
       <p><strong>Genre:</strong> {{ $release->genre }} ({{ $release->subgenre ?? 'No sub-genre' }})</p>
<p>
    <strong>Language:</strong> 
{{ $release->language ?? 'Default (English)' }} 
</p>
<p>
    <strong>Producers:</strong> 
{{ $release->credits ?? 'No Producer(s)' }} 
</p>
<p>
    <strong>Explicit:</strong> 
{{ $release->explicit ? 'Yes' : 'No' }}
</p>
<p>
    <strong>Label:</strong> 
{{ $release->label ?? 'No Label' }}
</p>
<p>
    <strong>Song Writer(s):</strong> 
{{ $release->songwriter ?? 'No Song Writer' }}
</p>

        <p><strong>Release Date:</strong> {{ $release->release_date }}</p>
        <p>
    <strong>UPC:</strong> {{ $release->upc ?? 'Not yet added'}}</p>
        <p><strong>Status:</strong>
            <span class="px-2 py-1 rounded text-white
                {{ $release->status === 'approved' ? 'bg-green-600' :
                   ($release->status === 'rejected' ? 'bg-red-600' : 'bg-yellow-600') }}">
                {{ ucfirst($release->status) }}
            </span>
        </p>
    </div>

    <div>
        <h3 class="font-semibold mb-1">Cover</h3>
        <img src="{{ assetPath($release->cover_path) }}"
             class="w-48 rounded border shadow">
    </div>

   <div>
    <h3 class="font-semibold mb-2">Tracks</h3>

    @foreach($release->tracks as $track)
        <div class="border rounded p-3 mb-3">
            <p><strong>Title:</strong> {{ $track->title }}</p>
            <p><strong>Artist:</strong> {{ $track->artist }}</p>
            <p><strong>Featured:</strong> {{ $track->featured_artists ?? 'None' }}</p>
            <p><strong>ISRC:</strong> {{ $track->isrc ?? 'Not added' }}</p>

            <audio controls class="w-full mt-2" preload="none">
                <source src="{{ route('admin.releases.audio.stream', $track->id) }}" type="audio/mpeg">
            </audio>

            @if($track->audio_path)
                <div class="mt-2">
                    <a href="{{ route('admin.releases.audio.download', $track->id) }}"
                       class="inline-block px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Download Track
                    </a>
                </div>
            @endif
        </div>
    @endforeach
</div>
</div>
@endsection
