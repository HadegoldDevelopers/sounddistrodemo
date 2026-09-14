@extends('layouts.admin.app')

@section('title', 'Artists')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Artists</h2>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Artist</th>
                    <th class="px-6 py-3 text-left font-semibold">Email</th>
                    <th class="px-6 py-3 text-left font-semibold">Genre</th>
                    <th class="px-6 py-3 text-left font-semibold">Music</th>
                    <th class="px-6 py-3 text-left font-semibold">Label</th>
                    <th class="px-6 py-3 text-left font-semibold">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($artists as $artist)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <!-- Artist Avatar + Name -->
                        <td class="px-6 py-4 flex items-center space-x-3">
                            @if($artist->profile_image)
                                <img src="{{ assetPath($artist->profile_image) }}" alt="{{ $artist->name }}" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($artist->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="font-semibold text-gray-900">{{ $artist->name }}</div>
                        </td>

                        <td class="px-6 py-4 text-gray-700">{{ $artist->email ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $artist->genre ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $artist->music->count() }}</td>
                        <td class="px-6 py-4">
                            @if($artist->label)
                                <div class="font-medium text-gray-800">{{ $artist->label->name }}</div>
                                <div class="text-xs text-gray-500">{{ $artist->label->email ?? '—' }}</div>
                            @else
                                <span class="text-gray-400 italic">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $artist->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No artists found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($artists as $artist)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-2">
                <div class="flex items-center space-x-3">
                    @if($artist->profile_image)
                        <img src="{{ assetPath($artist->profile_image) }}" alt="{{ $artist->name }}" class="h-12 w-12 rounded-full object-cover">
                    @else
                        <div class="h-12 w-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($artist->name, 0, 1)) }}
                        </div>
                    @endif
                    <h3 class="font-semibold text-lg text-gray-900">{{ $artist->name }}</h3>
                </div>

                <p class="text-sm text-gray-600">Email: {{ $artist->email ?? '—' }}</p>
                <p class="text-sm text-gray-700">Genre: <span class="font-medium">{{ $artist->genre ?? '—' }}</span></p>
                <p class="text-sm text-gray-700">Music: <span class="font-medium">{{ $artist->music->count() }}</span></p>
                <p class="text-sm text-gray-700">
                    Label: 
                    @if($artist->label)
                        <span class="font-medium">{{ $artist->label->name }}</span>
                    @else
                        <span class="italic text-gray-400">None</span>
                    @endif
                </p>
                <p class="text-sm text-gray-500">Joined: {{ $artist->created_at->format('M d, Y') }}</p>
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No artists found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
@include('components.pager', ['paginator' => $artists, '__pager' => $artists])

    </div>

</div>
@endsection
