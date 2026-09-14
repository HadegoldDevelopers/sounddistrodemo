@extends('layouts.admin.app')

@section('title', 'Submitted Releases')

@section('content')

<div class="w-full max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Submitted Releases</h1>
    </div>

    <!-- Status message -->
    @if(session('status'))
        <div class="mb-4 text-green-700 bg-green-100 p-4 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 text-gray-700 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Cover</th>
                    <th class="px-6 py-3 text-left font-semibold">Title</th>
                    <th class="px-6 py-3 text-left font-semibold">Type</th>
                    <th class="px-6 py-3 text-left font-semibold">Artist</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Copyright</th>
                    <th class="px-6 py-3 text-left font-semibold">Release Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody>
                @php($releases = $data)
                @forelse($releases as $project)
                    <tr class="border-b hover:bg-gray-50 transition">

                        <!-- Cover Thumbnail -->
                        <td class="px-6 py-4">
        @if($project->cover_path)
                <img src="{{ assetPath($project->cover_path) }}"
                     alt="Cover"
                     class="h-10 w-10 rounded object-cover shadow">
              @else
                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($project->title, 0, 1)) }}
                                </div>
              @endif

                        </td>

                        <!-- Title -->
                        <td class="px-6 py-4 max-w-[160px] truncate font-medium text-gray-900">
                            {{ $project->title }}
                        </td>
                        <!-- Type -->
                        <td class="px-6 py-4 max-w-[160px] truncate font-medium text-gray-900">
                            {{ $project->type }}
                        </td>
                        <!-- Artist -->
                        <td class="px-6 py-4 max-w-[150px] truncate">
                           {{ $project->artist_display }}
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full
                                {{ $project->status === 'approved' ? 'bg-green-100 text-green-700' :
                                   ($project->status === 'rejected' ? 'bg-red-100 text-red-700' :
                                   'bg-yellow-100 text-yellow-700') }}">
                                {{ $project->status }}
                            </span>
                        </td>

                        <!-- Copyright Flag -->
                        <td class="px-6 py-4">
                            @if($project->scan_state === 'blocked')
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-700">
                                    <span class="w-1.5 h-1.5 bg-pink-500 rounded-full"></span> Flagged
                                </span>
                                @foreach($project->copyright_flag as $flag)
                                    <p class="text-xs text-pink-600 mt-1 max-w-[180px] truncate"
                                       title="{{ $flag['matched_title'] ?? $flag['title'] }}{{ $flag['matched_artist'] ? ' — ' . $flag['matched_artist'] : '' }}">
                                        {{ $flag['matched_title'] ?? $flag['title'] }}{{ $flag['matched_artist'] ? ' — ' . $flag['matched_artist'] : '' }}
                                    </p>
                                @endforeach
                            @elseif($project->scan_state === 'error')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Scan failed</span>
                            @elseif($project->scan_state === 'pending')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Not scanned</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Clear</span>
                            @endif
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-gray-600">
                          {{ $project->release_date->format('M d, Y') }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div x-data="{open:false}" class="relative">
                                <button @click="open = !open"
                                    class="px-2 py-1 text-gray-700 hover:bg-gray-100 rounded">
                                    ⋮
                                </button>

                                <div x-show="open"
                                     @click.outside="open = false"
                                     class="absolute right-0 mt-2 bg-white shadow-lg rounded-md border w-44 z-20 py-1">

                                    <a href="{{ route('admin.releases.show', $project->id) }}"
                                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        View Details
                                    </a>

                                    <a href="{{ route('admin.releases.metadata.download', $project->id) }}"
                                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Download Metadata
                                    </a>
                                    <a href="{{ route('admin.releases.cover.download', $project->id) }}"
                                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Download cover
                                    </a>

                                    @if($project->status !== 'approved')
                                    <form action="{{ route('admin.releases.approve', $project->id) }}" method="POST">
                                        @csrf
                                        <button
                                            class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Approve
                                        </button>
                                    </form>
                                    @endif

                                    @if($project->status !== 'rejected')
                                    <form action="{{ route('admin.releases.reject', $project->id) }}" method="POST">
                                        @csrf
                                        <button
                                            class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Reject
                                        </button>
                                    </form>
                                    @endif
                                    
            <form action="{{ route('admin.releases.delete', $project->id) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this release? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button
                    class="w-full text-left flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                    Delete
                </button>
            </form>
                                </div>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No releases submitted yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">

        @php($mobileReleases = $data)
        @forelse($mobileReleases as $project)
            <div class="bg-white shadow border rounded-xl p-4 space-y-3">

                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-lg"> {{ $project->title }}
                    </h3>
                    <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full
                        {{ $project->status === 'approved' ? 'bg-green-100 text-green-700' :
                           ($project->status === 'rejected' ? 'bg-red-100 text-red-700' :
                           'bg-yellow-100 text-yellow-700') }}">
                        {{ $project->status }}
                    </span>
                </div>
                @if($project->scan_state === 'blocked')
                    <div class="flex items-center gap-1 mt-1">
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-700">
                            <span class="w-1.5 h-1.5 bg-pink-500 rounded-full"></span> Copyright Flagged
                        </span>
                        @foreach($project->copyright_flag as $flag)
                            <span class="text-xs text-pink-600 truncate"
                                  title="{{ $flag['matched_title'] ?? $flag['title'] }}{{ $flag['matched_artist'] ? ' — ' . $flag['matched_artist'] : '' }}">
                                {{ $flag['matched_title'] ?? $flag['title'] }}
                            </span>
                        @endforeach
                    </div>
                @elseif($project->scan_state === 'error')
                    <span class="inline-flex items-center gap-1 mt-1 px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Scan failed</span>
                @elseif($project->scan_state === 'pending')
                    <span class="inline-flex items-center gap-1 mt-1 px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Not scanned</span>
                @endif
                <p class="text-sm text-gray-600">Type: {{ $project->type }}</p>
                <p class="text-sm text-gray-600">Artist: {{ $project->artist_display }}</p>
@if($project->cover_path)
                <img src="{{ assetPath($project->cover_path) }}"
                     alt="Cover"
                     class="h-24 w-24 rounded object-cover shadow">
              @else
                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($project->title, 0, 1)) }}
                                </div>
              @endif
                <p class="text-sm text-gray-500">
                 {{ $project->release_date->format('M d, Y') }}
                </p>

                <div class="relative inline-block text-left">
    <button onclick="this.nextElementSibling.classList.toggle('hidden')" 
            class="inline-flex justify-center w-full rounded-md border px-4 py-2 bg-white text-sm font-medium shadow-sm hover:bg-gray-50">
        Actions
        <svg class="ml-2 -mr-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" 
                  d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" 
                  clip-rule="evenodd" />
        </svg>
    </button>

    <div class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1 text-sm">

            <a href="{{ route('admin.releases.show', $project->id) }}"
               class="block px-4 py-2 hover:bg-gray-100 text-indigo-600">
                View Details
            </a>

            <a href="{{ route('admin.releases.metadata.download', $project->id) }}"
               class="block px-4 py-2 hover:bg-gray-100 text-violet-600">
                Download Metadata
            </a>

            <a href="{{ route('admin.releases.cover.download', $project->id) }}"
               class="block px-4 py-2 hover:bg-gray-100 text-green-600">
                Download Cover
            </a>

            @if($project->status !== 'approved')
                <form action="{{ route('admin.releases.approve', $project->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-green-600">
                        Approve
                    </button>
                </form>
            @endif

            @if($project->status !== 'rejected')
                <form action="{{ route('admin.releases.reject', $project->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">
                        Reject
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>
            </div>

        @empty
            <p class="text-center text-gray-500 py-6">
                No releases submitted yet.
            </p>
        @endforelse
    </div>

    <!-- Pagination -->
@include('components.pager', ['paginator' => $data, '__pager' => $data])



</div>
@endsection
