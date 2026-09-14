@extends('layouts.admin.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Subscription Plans</h2>

        <a href="{{ route('admin.pricing.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
            + Create Plan
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Name</th>
                    <th class="px-6 py-3 text-left font-semibold">Amount</th>
                    <th class="px-6 py-3 text-left font-semibold">Billing Cycle</th>
                    <th class="px-6 py-3 text-left font-semibold">Role</th>
                    <th class="px-6 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($plans as $plan)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $plan->name }}</td>
                        <td class="px-6 py-4">
                            {{ formatCurrency($plan->price, 'USD') }}</td>
                        <td class="px-6 py-4">{{ $plan->billing_cycle }}
                        
                        </td>
                        <td class="px-6 py-4">{{ucfirst ($plan->role) }}
                        
                        </td>

                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('admin.pricing.edit', $plan) }}"
                               class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Edit
                            </a>

                            <form action="{{ route('admin.pricing.destroy', $plan) }}" method="POST"
                                  onsubmit="return confirm('Delete this plan?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No subscription plans found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="grid gap-4 md:hidden">
        @forelse($plans as $plan)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-3">
                <h3 class="font-semibold text-lg text-gray-900">{{ $plan->name }}</h3>

                <p class="text-sm text-gray-700">Amount: <span class="font-medium">{{ formatCurrency($plan->price, 'USD') }}
                </span></p>
                <p class="text-sm text-gray-700">Billing Cycle: <span class="font-medium">{{ucfirst ($plan->billing_cycle) }}</span></p>
                <p class="text-sm text-gray-700">Role: <span class="font-medium">{{ucfirst ($plan->role) }}</span></p>
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('admin.pricing.edit', $plan) }}"
                       class="px-3 py-1 bg-indigo-600 text-white rounded">Edit</a>

                    <form action="{{ route('admin.pricing.destroy', $plan) }}" method="POST"
                          onsubmit="return confirm('Delete this plan?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No subscription plans found.</p>
        @endforelse
    </div>
 <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
        @include('components.pager', ['paginator' => $plans, '__pager' => $plans])
    </div>
</div>
@endsection
