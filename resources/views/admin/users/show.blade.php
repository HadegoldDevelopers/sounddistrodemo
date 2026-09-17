@extends('layouts.admin.app')

@section('title', $label->label_name . ' — Label Details')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.labels.all') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to all labels</a>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight mt-1">{{ $label->label_name }}</h2>
        </div>

        <div>
            @if ($label->user && $label->user->is_active)
                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
            @else
                <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Inactive</span>
            @endif
        </div>
    </div>

    <!-- Label overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email</p>
            <p class="mt-1 font-medium text-gray-900">{{ $label->user->email ?? '—' }}</p>
        </div>
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Website</p>
            <p class="mt-1 font-medium text-gray-900">
                @if ($label->website)
                    <a href="{{ $label->website }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">{{ $label->website }}</a>
                @else
                    —
                @endif
            </p>
        </div>
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Joined</p>
            <p class="mt-1 font-medium text-gray-900">{{ optional($label->created_at)->format('M d, Y') }}</p>
        </div>
    </div>

    @if ($label->description)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Description</p>
            <p class="mt-2 text-gray-700">{{ $label->description }}</p>
        </div>
    @endif

    <!-- Artists -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-x-auto">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-gray-900">Artists ({{ $label->artist->count() }})</h3>
        </div>

        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Name</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Genre</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($label->artist as $artist)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $artist->name }}</td>
                        <td class="px-6 py-4">{{ $artist->genre ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $artist->user->email ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500">No artists under this label.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection