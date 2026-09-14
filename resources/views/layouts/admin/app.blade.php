<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ $global['meta_description'] }}">
    <meta name="keywords" content="{{ $global['meta_keywords'] }}">
   <title>@yield('title') | {{ $global['site_name']}}</title>

<link rel="icon" href="{{ asset('' . $global['favicon']) }}" type="image/x-icon">

@vite(['resources/css/app.css', 'resources/js/app.js'], 'temp')
 @stack('styles')    
    
</head>

<body
    x-data="{ 
        darkMode: JSON.parse(localStorage.getItem('darkMode') || 'false'),
        sidebarOpen: false 
    }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', JSON.stringify(val)))"
    :class="darkMode ? 'dark bg-gray-900 text-gray-100' : 'bg-gray-50 text-gray-900'"
    class="min-h-screen flex overflow-x-hidden"
>

<div class="flex min-h-screen overflow-x-hidden">

    <!-- Sidebar -->
    @include('layouts.admin.aside')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">

        <!-- Header -->
        @include('layouts.admin.header')

        <!-- Page Content -->
        <main class="flex-1 lg:p-10 overflow-x-hidden">
          @include('components.flash-messages')
            @yield('content')
        </main>

    </div>
</div>
@stack('scripts')
</body>
</html>