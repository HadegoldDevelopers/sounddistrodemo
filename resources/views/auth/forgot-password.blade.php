@extends('layouts.frontend')
@section('title','Forgot Password')

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

            <h2 class="text-2xl font-bold mb-2">Forgot Password</h2>
            <p class="text-sm text-gray-500 mb-6">
                Enter your email address below and we’ll send you a link to reset your password.
            </p>

            @include('layouts.partials.sessions')

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1">EMAIL</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('email')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3 bg-pink-500 text-white rounded-full font-semibold hover:bg-pink-600 transition">
                    <i class="fas fa-paper-plane"></i>
                    Send Reset Link
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
