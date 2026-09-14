@extends('layouts.admin.app')

@section('title', 'Active Subscribers')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
            Active Subscribers
        </h2>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">User</th>
                    <th class="px-6 py-3 text-left font-semibold">Email</th>
                    <th class="px-6 py-3 text-left font-semibold">Plan</th>
                    <th class="px-6 py-3 text-left font-semibold">Subscribed On</th>
                    <th class="px-6 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($subscribers as $user)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $user->name }}
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $user->email }}
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $user->plan_name ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4">
                            <a href="{{ route('admin.subscriptions.user', $user) }}"
                               class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                                View History
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No active subscribers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($subscribers as $user)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">
                <h3 class="font-semibold text-lg text-gray-900">{{ $user->name }}</h3>

                <p class="text-sm text-gray-700">Email: <span class="font-medium">{{ $user->email }}</span></p>
                <p class="text-sm text-gray-700">Plan: <span class="font-medium">{{ $user->plan_name ?? 'N/A' }}</span></p>
                <p class="text-sm text-gray-500">Subscribed: {{ $user->created_at->format('M d, Y') }}</p>

                <div class="pt-2">
                    <a href="{{ route('admin.subscriptions.user', $user) }}"
                       class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                        View History
                    </a>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No active subscribers found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $subscribers, '__pager' => $subscribers])
    </div>

</div>
@endsection
