@extends('layouts.admin.app')

@section('title', 'Edit Plan')

@section('content')
<div class="w-full max-w-full lg:max-w-3xl mx-auto px-4 py-6 space-y-8">

    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Subscription Plan</h2>

        <a href="{{ route('admin.pricing.plans') }}"
           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
            ← Back
        </a>
    </div>

    <form action="{{ route('admin.pricing.update', $plan) }}" method="POST"
          class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Plan Name</label>
            <input type="text" name="name" value="{{ $plan->name }}" class="w-full mt-1 p-2 border rounded" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Amount</label>
            <input type="number" step="0.01" name="price" value="{{ $plan->price }}" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label for="currency" class="block font-medium mb-1">Currency</label>
            <select name="currency" id="currency" class="w-full border p-2 rounded" required>
                <option value="">-- Select Currency --</option>
                <option value="USD" {{ old('currency', $plan->currency) == 'USD' ? 'selected' : '' }}>USD</option>
            </select>
            @error('currency') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label for="role" class="block font-medium mb-1">Role</label>
            <select name="role" id="role" class="w-full border p-2 rounded" required>
                <option value="">-- Select Role --</option>
                <option value="artist" {{ old('role', $plan->role) == 'artist' ? 'selected' : '' }}>Artist</option>
                <option value="label" {{ old('role', $plan->role) == 'label' ? 'selected' : '' }}>Label</option>
            </select>
            @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label for="billing_cycle" class="block font-medium mb-1">Billing Cycle</label>
            <select name="billing_cycle" id="billing_cycle" class="w-full border p-2 rounded" required>
                <option value="">-- Select Billing Cycle --</option>
                <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) == 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
            @error('billing_cycle') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description (optional)</label>
            <textarea name="description" id="description" rows="3" class="w-full border p-2 rounded">{{ old('description', $plan->description) }}</textarea>
            @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Update Plan
        </button>
    </form>

</div>
@endsection
