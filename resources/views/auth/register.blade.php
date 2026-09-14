@extends('layouts.frontend')

@section('title', 'Sign up')

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
            <h2 class="text-2xl font-bold mb-1">Start for free</h2>
            <p class="text-sm text-gray-500 mb-8">Create your {{ $global['site_name'] }} account today</p>

            @include('layouts.partials.sessions')

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-600 mb-1">Artist/Label Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                        placeholder="Your name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('name')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1">EMAIL</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('email')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1">PASSWORD</label>
                    <input id="password" name="password" type="password" required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('password')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 mb-1">CONFIRM PASSWORD</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    @error('password_confirmation')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block text-xs font-semibold text-gray-600 mb-1">ROLE</label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="" disabled selected>Select role</option>
                        <option value="artist" {{ old('role') === 'artist' ? 'selected' : '' }}>Artist</option>
                        @if ($global['allow_label_registration'])
                            <option value="label" {{ old('role') === 'label' ? 'selected' : '' }}>Label</option>
                        @endif
                    </select>
                    @error('role')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-center space-x-2">
                    <input id="terms" name="terms" type="checkbox" required
                        class="h-4 w-4 text-pink-500 border-gray-300 rounded focus:ring-pink-500">
                    <label for="terms" class="text-sm text-gray-700">
                        By signing up, you agree to our
                        <a href="/terms" target="_blank" class="text-pink-500 underline">Terms</a> and
                        <a href="/privacy" target="_blank" class="text-pink-500 underline">Privacy Policy</a>.
                    </label>
                </div>
                @error('terms')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3 bg-pink-500 text-white rounded-full font-semibold hover:bg-pink-600 transition flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    CREATE ACCOUNT
                </button>
            </form>

            <!-- Link to Login -->
            <p class="mt-6 text-center text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="text-pink-500 font-semibold hover:underline">Log in</a>
            </p>
        </div>
    </div>
</div>
@endsection
