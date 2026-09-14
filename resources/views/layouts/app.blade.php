<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ $global['site_name']}}</title>
    
@if (!request()->routeIs('login', 'register', 'password.request', 'password.reset'))
<link rel="canonical" href="{{ url()->current() }}">
@endif
@if (request()->routeIs('login', 'register', 'password.request', 'password.reset'))
<meta name="robots" content="noindex, nofollow">
@endif

    <meta name="description" content="{{ $global['meta_description'] }}">
    <meta name="keywords" content="{{ $global['meta_keywords'] }}">
<meta name="google-site-verification" content="{{ $global['gsiteverify'] }}" />
    <link rel="icon" href="{{ asset('' . $global['favicon']) }}">
@vite(['resources/css/app.css', 'resources/js/app.js'], 'temp')
<!-- Inter Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <!-- Alpine.js -->
 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>

<style>
 html {
  scroll-behavior: smooth;
  scroll-padding-top: 80px; 
}
    body {
      font-family: 'Inter', sans-serif;
    }

    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #1a1a1a;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
      background: #4a4a4a;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #6a6a6a;
    }
    
  </style>
  <style>
  .swiper-button-prev::after,
  .swiper-button-next::after {
    font-size: 18px;
    font-weight: 600;
  }
/*  .milestoneSwiper .swiper-slide {*/
/*  height: 620px !important;*/
/*  display: flex;*/
/*  align-items: stretch;*/
/*}*/

</style>

</head>

<body class="h-full text-gray-800 antialiased">
<header x-data="{ open: false }" class="bg-white shadow sticky top-0 z-50 h-20">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo -->
<a href="{{ route('home') }}" class="flex items-center text-orange-500 text-2xl font-bold">
    <img src="{{ asset($global['site_logo']) }}" 
     alt="{{ $global['site_name'] }}" 
   class="h-16 w-auto object-contain">
</a>


        <!-- Desktop Menu -->
        <nav class="hidden md:flex space-x-6">
            <a href="{{ route('home') }}/#home" class="hover:text-indigo-600">Home</a>
            <a href="{{ route('home') }}/#services" class="hover:text-indigo-600">Services</a>
            <a href="{{ route('home') }}/#pricing" class="hover:text-indigo-600">Pricing</a>
            <a href="{{ route('home') }}/#contact" class="hover:text-indigo-600">Contact</a>

            @auth
    <a href="{{ url('/dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
@else
    <a href="{{ route('login') }}" class="hover:text-indigo-600">Log in</a>

    @if ($global['allow_user_registration'])
        <a href="{{ route('register') }}"
           class="inline-block px-5 py-1.5 border text-sm rounded-sm hover:border-indigo-300 transition">
           Sign up
        </a>
    @endif
@endauth

        </nav>

        <!-- Mobile Hamburger Button -->
        <button @click="open = !open" class="md:hidden text-gray-700 focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" @click.outside="open = false" class="md:hidden bg-white shadow-md">
        <nav class="flex flex-col space-y-3 px-6 py-4">
            <a href="{{ route('home') }}/#home" class="hover:text-indigo-600">Home</a>
            <a href="{{ route('home') }}/#services" @click="open = false" class="hover:text-indigo-600">Services</a>
            <a href="{{ route('home') }}/#pricing" @click="open = false" class="hover:text-indigo-600">Pricing</a>
            <a href="{{ route('home') }}/#contact" @click="open = false" class="hover:text-indigo-600">Contact</a>

           @auth
    <a href="{{ url('/dashboard') }}" @click="open = false">Dashboard</a>
@else
    <a href="{{ route('login') }}" @click="open = false">Log in</a>

    @if ($global['allow_user_registration'])
        <a href="{{ route('register') }}" @click="open = false">Sign up</a>
    @endif
@endauth

        </nav>
    </div>
</header>
    <main class="min-h-screen">
        @yield('content')
    </main>
 
  <!-- Footer -->
@if (request()->routeIs('login', 'register', 'password.request', 'password.reset'))
    {{-- no footer --}}
@else
 <footer id="contact" class="bg-gray-900 text-gray-300 py-10">
  <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">

    <!-- Company Info -->
    <div>
        @if(!empty($global['site_name']))
            <h3 class="text-white text-lg font-semibold mb-3">{{ $global['site_name'] }}</h3>
        @endif

        @if(!empty($global['site_tag']))
            <p class="text-sm mb-2">{{ $global['site_tag'] }}</p>
        @endif

        @if(!empty($global['email']))
            <p class="text-sm">
                <span class="font-medium text-white">Email:</span>
                <a href="mailto:{{ $global['email'] }}" class="hover:text-indigo-400">{{ $global['email'] }}</a>
            </p>
        @endif

        @if(!empty($global['phone']))
            <p class="text-sm">
                <span class="font-medium text-white">Phone:</span>
                <a href="tel:{{ $global['phone'] }}" class="hover:text-indigo-400">{{ $global['phone'] }}</a>
            </p>
        @endif

        @if(!empty($global['address']))
            <p class="text-sm">
                <span class="font-medium text-white">Office Address:</span>
                {{ $global['address'] }}
            </p>
        @endif
    </div>

    <!-- Social Media -->
    <div>
        <h4 class="text-white font-semibold mb-3">Connect With Us</h4>
        <div class="flex justify-center md:justify-start space-x-8 text-xl">
            @if(!empty($global['facebook']))
                <a href="{{ $global['facebook'] }}" target="_blank" class="hover:text-indigo-400"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(!empty($global['twitter']))
                <a href="{{ $global['twitter'] }}" target="_blank" class="hover:text-indigo-400"><i class="fab fa-twitter"></i></a>
            @endif
            @if(!empty($global['instagram']))
                <a href="{{ $global['instagram'] }}" target="_blank" class="hover:text-indigo-400"><i class="fab fa-instagram"></i></a>
            @endif
            @if(!empty($global['linkedin']))
                <a href="{{ $global['linkedin'] }}" target="_blank" class="hover:text-indigo-400"><i class="fab fa-linkedin-in"></i></a>
            @endif
        </div>
    </div>

    <!-- Legal Links -->
    <div>
        <h4 class="text-white font-semibold mb-3">Legal</h4>
        <ul class="space-y-2 text-sm">
            <li><a href="{{ route('privacy.policy') }}" class="hover:text-indigo-400">Privacy Policy</a></li>
            <li><a href="{{ route('terms') }}" class="hover:text-indigo-400">Terms & Conditions</a></li>
            <li><a href="{{ route('cookies.page') }}" class="hover:text-indigo-400">Cookie Policy</a></li>
            <li><a href="{{ route('refund.page') }}" class="hover:text-indigo-400">Refund Policy</a></li>
        </ul>
    </div>
  </div>
  <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm">
    <p>&copy; {{ now()->year }} {{ $global['site_name'] }}. All rights reserved.</p>
  </div>
</footer>
@endif


<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
  const swiper = new Swiper(".trendingSwiper", {
    loop: true, 
    spaceBetween: 20,
    slidesPerView: 1,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false, 
    },
    breakpoints: {
      640: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
    },
  });
</script>
<script>
 const milestoneSwiper = new Swiper('.milestoneSwiper', {
  slidesPerView: 1,
  loop: true,
  speed: 800,
  autoplay: {
    delay: 4000,              
    disableOnInteraction: false,
    pauseOnMouseEnter: true 
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
});

</script>


</body>

</html>
