<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title') | {{ $global['site_name']}}</title>
  
  <meta name="description" content="{{ $global['meta_description'] }}">
  <meta name="keywords" content="{{ $global['meta_keywords'] }}">
  
  <link rel="icon" href="{{ asset('' . $global['favicon']) }}" type="image/x-icon">

  <!-- Tailwind CSS & Vite -->
@vite(['resources/css/app.css', 'resources/js/app.js'], 'temp')

  <!-- Inter Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <!-- Alpine.js -->
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body x-data="{ sidebarOpen: false }" class="bg-zinc-950 text-gray-100 min-h-screen flex overflow-x-hidden">

  <!-- Wrapper -->
  <div class="flex min-h-screen w-full overflow-hidden relative">

    <!-- Overlay for mobile -->
    <div 
      x-show="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-30 bg-black bg-opacity-50 md:hidden"
      x-transition:enter="transition-opacity ease-out duration-200"
      x-transition:leave="transition-opacity ease-in duration-150"
      x-cloak>
    </div>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-screen w-64 bg-zinc-900 p-6 z-40 transform md:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto"
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    x-cloak>
      
      <!-- Close button on mobile -->
      <div class="flex justify-end md:hidden mb-4">
        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      @include('layouts.partials.sidebar')
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-64">
      <!-- Header -->
      <header class="bg-zinc-900 p-4 flex items-center justify-between shadow-lg z-10">
        @php
    $route = Route::currentRouteName();

    $titles = [
        'user.dashboard' => 'Dashboard Overview',
        'profile.edit' => 'Profile Settings',
        'user.settings' => 'User Settings',
        'user.releases' => 'My Releases',
        'artists.index' => 'Label Artists Management',
        'music.upload' => 'New Release',
        'user.stats'   => 'Analytics',
        'payment.index' => 'Subscriptions',
        'royalties.index' => 'Royalties',
        'earnings.withdraw' => 'Withdraw Royalties',
    ];

    $pageTitle = $titles[$route] ?? 'Dashboard';
@endphp
 <div class="flex items-center space-x-4">
    <!-- Hamburger always visible on mobile -->
    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 text-xl hover:text-white focus:outline-none">
      <i class="fas fa-bars"></i>
    </button>

    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $pageTitle }}</h1>
  </div>

  <div class="flex items-center space-x-4">
    
    <!-- New Release button -->
    <a href="{{ route('music.upload') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105 flex items-center text-base hidden sm:flex">
      <i class="fas fa-plus mr-2"></i> {{ __("New Release") }}
    </a>

    <!-- New Release icon for mobile -->
    <a href="{{ route('music.upload') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-md transition duration-300 sm:hidden flex items-center justify-center">
      <i class="fas fa-plus"></i>
    </a>
  </div>
      </header>
@include('layouts.partials.sessions')
      <!-- Page Content -->
      <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
        @yield('content')
      </main>
    </div>
  </div>
@stack('scripts')
</body>
</html>