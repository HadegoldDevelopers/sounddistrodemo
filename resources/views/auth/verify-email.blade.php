@extends('layouts.frontend')

@section('title', 'Verify Your Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-6 py-12">
    
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">
        
        <!-- Mail Icon -->
        <div class="flex justify-center mb-6">
            <div class="h-16 w-16 flex items-center justify-center rounded-full bg-purple-100">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-8 w-8 text-purple-600" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor" 
                     stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8l9 6 9-6M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <h2 class="text-2xl font-bold text-gray-800 mb-3">
            Verify Your Email Address
        </h2>

        <!-- Description -->
<p class="text-gray-600 text-sm mb-2 leading-relaxed">
    We’ve sent a verification link to
</p>

<p class="font-semibold text-gray-800 mb-4">
    {{ auth()->user()->email }}
</p>

<p class="text-gray-600 text-sm mb-6">
    Please check your inbox and click the link to activate your account.
</p>

<!-- Change Email Link -->
<div class="mb-6">
    <a href="{{ route('verification.change') }}"
       class="text-sm text-purple-600 hover:text-purple-700 font-medium">
        Wrong email? Change it
    </a>
</div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 flex items-center justify-center gap-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 p-3 rounded-lg">
                
                <!-- Check Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-5 w-5" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor" 
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>

                A new verification link has been sent.
            </div>
        @endif

        <!-- Actions -->
        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-500 hover:text-gray-700 transition underline">
                    Log out
                </button>
            </form>
        </div>

    </div>
</div>
@endsection