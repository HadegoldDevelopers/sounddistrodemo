@extends('layouts.frontend')

@section('title', 'Change Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-6 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Change Email Address
        </h2>

        <form method="POST" action="{{ route('verification.change.update') }}" class="space-y-4">
            @csrf

            <input type="email" name="email"
                value="{{ old('email', auth()->user()->email) }}"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">

            @error('email')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                Update Email
            </button>
        </form>

    </div>
</div>
@endsection