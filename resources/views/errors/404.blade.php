@extends('layouts.app')
@section('title', 'Page Not Found')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center px-6 py-20 text-center">

    <h1 class="text-7xl font-extrabold text-primary mb-4">404</h1>

    <h2 class="text-2xl font-semibold text-gray-800 mb-3">
        Page Not Found
    </h2>

    <p class="text-gray-500 max-w-md mb-8">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
    </p>

    <a href="{{ route('home') }}"
       class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition font-medium">
        Go Back Home
    </a>

</div>

@endsection
