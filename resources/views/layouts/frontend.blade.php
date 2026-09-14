<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') | {{ $global['site_name'] }}</title>

  @if(request()->routeIs('login','register','password.request','password.reset'))
    <meta name="robots" content="noindex, nofollow">
  @else
    <link rel="canonical" href="{{ url()->current() }}">
  @endif

  <meta name="description" content="{{ $global['meta_description'] }}">
  <meta name="keywords" content="{{ $global['meta_keywords'] }}">
  <meta name="google-site-verification" content="{{ $global['gsiteverify'] }}" />
  <link rel="icon" href="{{ asset($global['favicon']) }}">

  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>

  <script src="https://cdn.tailwindcss.com"></script>
   @stack('styles')
</head>

<body class="h-full text-gray-800 antialiased font-inter">

  {{-- Hide header on auth pages --}}
  @if(!request()->routeIs('login','register','password.request','password.reset'))
    <header id="site-header" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
      <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2">
          <img src="{{ asset($global['site_logo']) }}" alt="{{ $global['site_name'] }}" class="h-16 w-auto object-contain">
        </a>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-700 uppercase tracking-wide">
          @php
    $sections = ['distribution'];

    if(!empty($global['require_subscription']) && $global['require_subscription']) {
        $sections[] = 'pricing';
    }

    $sections[] = 'artists';
    $sections[] = 'contact';
@endphp

@foreach($sections as $section)
    <a href="#{{ $section }}" class="hover:text-pink-500 transition">
        {{ ucfirst($section) }}
    </a>
@endforeach


          @auth
            <a href="{{ url('/dashboard') }}" class="hover:text-pink-500 transition">Dashboard</a>
          @else
            <a href="{{ route('login') }}" class="hover:text-pink-500 transition">Log In</a>
            @if($global['allow_user_registration'])
              <a href="{{ route('register') }}" class="px-5 py-2 rounded-full bg-pink-500 text-white hover:bg-pink-600 transition">
                Sign Up Free
              </a>
            @endif
          @endauth
        </nav>

        <!-- Mobile Hamburger -->
        <button id="mobile-toggle" class="md:hidden text-gray-700 focus:outline-none">
          <i class="fas fa-bars text-2xl"></i>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div id="mobile-menu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 bg-white border-t border-gray-200">
        <nav class="flex flex-col space-y-3 px-6 py-4 text-gray-700 font-semibold uppercase tracking-wide">
          @foreach(['distribution','pricing','artists','contact'] as $section)
            <a href="#{{ $section }}" class="hover:text-pink-500 transition">{{ ucfirst($section) }}</a>
          @endforeach

          @auth
            <a href="{{ url('/dashboard') }}" class="hover:text-pink-500 transition">Dashboard</a>
          @else
            <a href="{{ route('login') }}" class="hover:text-pink-500 transition">Log In</a>
            @if($global['allow_user_registration'])
              <a href="{{ route('register') }}" class="px-5 py-2 rounded-full bg-pink-500 text-white hover:bg-pink-600 transition text-center">
                Sign Up Free
              </a>
            @endif
          @endauth
        </nav>
      </div>
    </header>
  @endif

  <main class="min-h-screen">
    @yield('content')
  </main>

  {{-- Hide footer on auth pages --}}
  @if(!request()->routeIs('login','register','password.request','password.reset'))
    @include('layouts.partials.footer')
  @endif

  <script src="{{ asset('script.js') }}"></script>
 @stack('script')
</body>
</html>
