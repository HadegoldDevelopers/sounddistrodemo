@extends('layouts.frontend')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex">

<x-auth-side-panel />

    <!-- Right Panel -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-white px-6 py-12">
    <div class="max-w-md w-full"> 
    <a href="{{ route('home') }}" class="auth-logo-mobile md:hidden block text-center mb-6">
    <img src="{{ asset($global['site_logo']) }}" class="h-16 w-auto mx-auto">
    </a>
            <h2 class="text-2xl font-bold mb-2">Welcome back</h2>
            <p class="text-sm text-gray-500 mb-6">Sign in to your {{ $global['site_name'] }} account</p>

            @include('layouts.partials.sessions')

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

<input type="email" name="email" placeholder="you@example.com" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">

               <div class="relative">
<input type="password" name="password" id="password-field" placeholder="••••••••" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">

    <!-- Toggle Icon -->
<button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
        <i id="password-icon" class="fas fa-eye text-lg"></i>
    </button>

    <a href="{{ route('password.request') }}" 
       class="absolute right-12 top-3 text-sm text-pink-500 font-semibold hover:underline">
        Forgot?
    </a>
</div>

<button type="submit"
    class="w-full py-3 bg-pink-500 text-white rounded-lg font-semibold hover:bg-pink-600 transition flex items-center justify-center gap-2">
    <i class="fas fa-sign-in-alt"></i>
    Log in
</button>

            </form>

            @if($global['allow_user_registration'])
                <p class="mt-6 text-sm text-center text-gray-600">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="text-pink-500 font-semibold hover:underline">
                        Sign up free
                    </a>
                </p>
            @else
                <p class="mt-6 text-sm text-center text-gray-600">
                    Want to join our platform?
                    <a href="{{ route('home') }}#contact" class="text-pink-500 font-semibold hover:underline">
                        Apply now
                    </a>
                </p>
            @endif
        </div>
    </div>

</div>
@push('script')
<script>
function togglePassword() {
    const field = document.getElementById('password-field');
    const icon = document.getElementById('password-icon');

    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@endpush
@push('styles')
<style>
/* Hide the entire left panel on mobile */
@media (max-width: 767px) {
    .auth-left-panel {
        display: none !important;
    }
}
</style>
@endpush
@endsection
