@extends('layouts.admin.app')

@section('title', 'All Labels')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">All Labels</h2>

    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white border border-gray-200 shadow-sm rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Label Name</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Artists</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Joined</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($labels as $label)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $label->label_name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $label->user->email }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $label->artist->count() }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($label->user->is_active)
                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            {{ $label->created_at->format('M d, Y') }}
                        </td>

                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            No labels found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse ($labels as $label)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-2">

                <div class="flex justify-between">
                    <h3 class="font-semibold text-lg text-gray-900">
                        {{ $label->label_name }}
                    </h3>

                    @if ($label->user->is_active)
                        <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                            Active
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                            Inactive
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600">{{ $label->user->email }}</p>

                <p class="text-sm text-gray-700">
                    Artists: <span class="font-medium">{{ $label->artist->count() }}</span>
                </p>

                <p class="text-sm text-gray-500">
                    Joined: {{ $label->created_at->format('M d, Y') }}
                </p>


            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No labels found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
@include('components.pager', ['paginator' => $labels, '__pager' => $labels])

    </div>

</div>
@endsection
