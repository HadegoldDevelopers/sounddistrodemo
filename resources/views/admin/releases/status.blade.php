@extends('layouts.admin.app')

@section('title', ucfirst($status) . ' Releases')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

  <!-- Page Title -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h1 class="text-2xl font-bold text-gray-900">{{ ucfirst($status) }} Releases</h1>
  </div>

  <!-- Success Message -->
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
          <th class="px-6 py-3 text-left font-semibold">Release Date</th>
          <th class="px-6 py-3 text-left font-semibold">Status</th>
          <th class="px-6 py-3 text-left font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $release)
          <tr class="border-b hover:bg-gray-50 transition">

            <!-- Cover -->
            <td class="px-6 py-4">
              @if($release->cover_path)
                <img src="{{ assetPath($release->cover_path) }}"
                     alt="Cover"
                     class="h-12 w-12 rounded object-cover shadow">
              @else
                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($release->title, 0, 1)) }}
                                </div>
              @endif
            </td>

            <!-- Title -->
            <td class="px-6 py-4 font-medium text-gray-900">{{ $release->title }}</td>

            <!-- Type -->
            <td class="px-6 py-4">{{ ucfirst($release->type) }}</td>

            <!-- Artist -->
            <td class="px-6 py-4">
                {{ $release->artist_display ?? ($release->tracks->first()?->artist ?? 'N/A') }}
            </td>

            <!-- Release Date -->
            <td class="px-6 py-4">{{ $release->release_date }}</td>

            <!-- Status -->
            <td class="px-6 py-4">
              <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full
                {{ $release->status === 'approved' ? 'bg-green-100 text-green-700' :
                   ($release->status === 'rejected' ? 'bg-red-100 text-red-700' :
                   'bg-yellow-100 text-yellow-700') }}">
                {{ $release->status }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 flex flex-col gap-2 md:flex-row md:gap-4">
              <div x-data="{open:false}" class="relative">
                <button @click="open = !open"
                        class="px-2 py-1 text-gray-700 hover:bg-gray-100 rounded">
                  ⋮
                </button>

                <div x-show="open"
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 bg-white shadow-lg rounded-md border w-44 z-20 py-1">

                    <a href="{{ route('admin.releases.show', $release->id) }}"
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        View Details
                    </a>
                    <a href="{{ route('admin.releases.metadata.download', $release->id) }}"
                                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Download Metadata
                                    </a>
                    <a href="{{ route('admin.releases.cover.download', $release->id) }}"
                                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Download Cover
                                    </a>
                    @if($release->status !== 'approved')
                    <form action="{{ route('admin.releases.approve', $release->id) }}" method="POST">
                        @csrf
                        <button class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Approve
                        </button>
                    </form>
                    @endif

                    @if($release->status !== 'rejected')
                    <form action="{{ route('admin.releases.reject', $release->id) }}" method="POST">
                        @csrf
                        <button class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Reject
                        </button>
                    </form>
                    @endif
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No {{ $status }} releases yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Mobile Cards -->
  <div class="grid gap-4 md:hidden">
    @forelse($data as $release)
      <div class="bg-white border border-gray-200 shadow rounded-xl p-4 space-y-3">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold text-lg text-gray-900">{{ $release->title }}</h3>
          <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full
            {{ $release->status === 'approved' ? 'bg-green-100 text-green-700' :
               ($release->status === 'rejected' ? 'bg-red-100 text-red-700' :
               'bg-yellow-100 text-yellow-700') }}">
            {{ $release->status }}
          </span>
        </div>

        <p class="text-sm text-gray-600">Type: {{ ucfirst($release->type) }}</p>
        <p class="text-sm text-gray-600">Artist: {{ $release->artist_display ?? ($release->tracks->first()?->artist ?? 'N/A') }}</p>
        <p class="text-sm text-gray-600">Release Date: {{ $release->release_date }}</p>
        @if($release->cover_path)
          <img src="{{ assetPath($release->cover_path) }}" alt="Cover" class="h-24 w-24 rounded object-cover shadow">
         @else
                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($release->title, 0, 1)) }}
                                </div>
        @endif
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

            <a href="{{ route('admin.releases.show', $release->id) }}"
               class="block px-4 py-2 hover:bg-gray-100 text-indigo-600">
                View Details
            </a>

            <a href="{{ route('admin.releases.metadata.download', $release->id) }}"
               class="block px-4 py-2 hover:bg-gray-100 text-violet-600">
                Download Metadata
            </a>

            <a href="{{ route('admin.releases.cover.download', $release->id) }}"
               class="block px-4 py-2 hover:bg-gray-100 text-green-600">
                Download Cover
            </a>

            @if($release->status !== 'approved')
                <form action="{{ route('admin.releases.approve', $release->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-green-600">
                        Approve
                    </button>
                </form>
            @endif

            @if($release->status !== 'rejected')
                <form action="{{ route('admin.releases.reject', $release->id) }}" method="POST">
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
      <p class="text-center text-gray-500 py-6">No {{ $status }} releases yet.</p>
    @endforelse
  </div>

  <!-- Pagination -->
  <div class="pt-4 flex justify-center text-indigo-600">
    @include('components.pager', ['paginator' => $data, '__pager' => $data])
  </div>

</div>
@endsection