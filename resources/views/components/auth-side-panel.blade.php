<!-- Left Panel -->
<div class="w-1/2 hidden md:flex flex-col justify-center items-center bg-gradient-to-br from-pink-500 via-purple-500 to-orange-400 relative">

    <!-- Logo (Top‑Left) -->
    <a href="{{ route('home') }}" class="absolute top-6 left-6 z-20">
        <img src="{{ asset($global['site_logo']) }}" 
             alt="{{ $global['site_name'] }}" 
             class="h-12 w-auto object-contain drop-shadow-lg">
    </a>
<div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-gradient-to-br from-pink-500 via-purple-500 to-orange-400 relative p-10 md:p-0">
    <a href="{{ route('home') }}" class="z-20 mb-6 md:hidden">
        <img src="{{ asset($global['site_logo']) }}" class="h-12 w-auto">
    </a>
</div>

    <!-- Foreground Content -->
    <div class="relative z-10 text-white px-10 py-16 text-left max-w-md">
        <h4 class="text-sm font-semibold text-yellow-300 mb-2">FOR CREATORS</h4>
        <h1 class="text-5xl font-extrabold leading-tight mb-4">
            Your music.<br>Your rules.<br>Your money.
        </h1>
        <p class="text-sm text-pink-100 mb-12">
            Get your songs on 150+ platforms and earn every month with {{ $global['site_name'] }}.
        </p>

        @php
            $selectedPartners = [
                'Spotify', 'Apple Music', 'Amazon Music', 'YouTube Music',
                'Boomplay', 'TikTok', 'Anghami', 'Deezer', 'Tidal', 'Facebook'
            ];

            $partners = array_filter(
                config('stores'),
                fn($key) => in_array($key, $selectedPartners),
                ARRAY_FILTER_USE_KEY
            );
        @endphp

        <!-- Store Icons -->
        <div class="flex flex-wrap gap-4 mt-6">
            @foreach ($partners as $name => $cfg)
                <div class="h-12 w-12 rounded-full flex items-center justify-center bg-white/90 backdrop-blur">
                    <img src="{{ asset('images/icons/' . $cfg['icon']) }}"
                         alt="{{ $name }}"
                         class="h-6 w-6 object-contain"
                         loading="lazy">
                </div>
            @endforeach
        </div>
    </div>

    <!-- Decorative Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
</div>
