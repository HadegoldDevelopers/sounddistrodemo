@extends('layouts.frontend')

@section('title','Reset Password')

@section('content')
<div class="min-h-screen flex">

    <x-auth-side-panel />

    <!-- Right Panel -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-white px-6 py-12">
        <div class="max-w-md w-full">

            <!-- Mobile Logo -->
            <a href="{{ route('home') }}" class="md:hidden block text-center mb-6">
                <img src="{{ asset($global['site_logo']) }}" class="h-16 w-auto mx-auto">
            </a>

            <!-- Heading -->
            <h2 class="text-2xl font-bold mb-2">Reset Your Password</h2>
            <p class="text-sm text-gray-500 mb-6">Enter your new password below to regain access.</p>

            @include('layouts.partials.sessions')

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1">EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                           placeholder="you@example.com"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('email')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1">NEW PASSWORD</label>
                    <input id="password" type="password" name="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('password')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 mb-1">CONFIRM PASSWORD</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('password_confirmation')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full py-3 bg-pink-500 text-white rounded-full font-semibold hover:bg-pink-600 transition flex items-center justify-center gap-2">
                    <i class="fas fa-key"></i>
                    Reset Password
                </button>
            </form>

            <!-- Link to Login -->
            <p class="mt-6 text-center text-sm text-gray-600">
                Remember your password?
                <a href="{{ route('login') }}" class="text-pink-500 font-semibold hover:underline">Log in</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media (max-width: 767px) {
    x-auth-side-panel {
        display: none !important;
    }
}
</style>
@endpush
