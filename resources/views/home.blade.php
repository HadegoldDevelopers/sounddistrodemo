@extends('layouts.frontend')
@section('title', $global['site_title'])

@section('content')
  {{-- Hero --}}
  <section id="distribution"
           class="relative min-h-[90vh] flex flex-col md:flex-row items-center justify-between px-6 md:px-12 lg:px-20 overflow-hidden bg-[linear-gradient(135deg,#A020F0_0%,#FF4F81_50%,#FF8C00_100%)]">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-16 left-1/2 w-2 h-10 bg-yellow-300 rotate-45"></div>
      <div class="absolute top-24 left-[60%] w-2 h-10 bg-yellow-300 -rotate-45"></div>
      <div class="absolute top-20 right-[20%] w-2 h-10 bg-yellow-300 rotate-12"></div>
    </div>

    <div class="md:w-1/2 flex justify-center md:justify-start mt-12 md:mt-0">
      <div class="relative">
        <img src="{{ asset($homepage->hero_image) }}" alt="Hero Image" class="rounded-lg shadow-lg w-[320px] md:w-[400px]">
        <a href="#watch"
           class="absolute bottom-4 left-4 bg-white text-black text-xs font-semibold px-4 py-2 rounded-full flex items-center gap-2 shadow-md hover:bg-gray-100 transition">
          <span class="w-2 h-2 bg-red-500 rounded-full"></span> WATCH &amp; LISTEN
        </a>
      </div>
    </div>

    <div class="md:w-1/2 text-right md:text-left mt-12 md:mt-0 space-y-6">
      <h1 class="text-6xl md:text-7xl font-extrabold leading-tight">
        <span class="block text-white">{{ $homepage->hero_title }}</span>
      </h1>
      <p class="text-white text-lg max-w-md leading-relaxed">
        {{ $homepage->hero_text }}
      </p>

      <x-cta-button color="yellow" class="mt-6" />
    </div>
  </section>

  {{-- Features Band --}}
  <section class="bg-[#4FD1C5] py-16 text-black">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8 text-sm font-semibold uppercase tracking-wide">
      @foreach($features as $feature)
        <p class="flex items-center gap-2">
          <i class="fas {{ $feature['icon'] }} text-pink-500"></i>
          {{ $feature['text'] }}
        </p>
      @endforeach
    </div>
  </section>

  {{-- Labels --}}
  <section class="bg-gray-50 py-20 px-6 md:px-12 lg:px-20">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
      <div>
        <h2 class="text-3xl md:text-4xl font-extrabold text-black mb-4">
          {{ $homepage->labels_title }}
        </h2>
        <p class="text-gray-600 mb-6">
          {{ $homepage->labels_text }}
        </p>
        <ul class="space-y-3 text-gray-700 font-medium">
          <li class="flex items-center gap-2">
            <i class="fas fa-bolt text-red-500"></i>
            Fast Music Delivery &amp; Monetization
          </li>
          <li class="flex items-center gap-2">
            <i class="fas fa-layer-group text-red-500"></i>
            Scalable Solutions for Labels
          </li>
          <li class="flex items-center gap-2">
            <i class="fas fa-chart-line text-red-500"></i>
            Advanced Royalty &amp; Catalog Management
          </li>
        </ul>
      </div>

      <div class="grid grid-cols-3 gap-6 justify-items-center">
        @foreach($partners as $name => $cfg)
          <div class="w-16 h-16 bg-white rounded-xl shadow flex items-center justify-center transform transition-transform duration-500 hover:scale-110 hover:shadow-lg">
            <img src="{{ asset('images/icons/' . $cfg['icon']) }}" alt="{{ $name }}" class="w-10 h-10">
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Release Your Creativity --}}
  <section class="bg-yellow-300 py-24 px-6 md:px-12 lg:px-20 text-black">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
      <div>
        <img src="{{ asset($homepage->labels_image) }}" alt="Canoe" class="rounded-lg shadow-lg w-[320px] md:w-[400px]">
      </div>
      <div>
        <h2 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6">
          Release your <span class="text-pink-500">creativity.</span>
        </h2>
        <p class="text-lg leading-relaxed mb-6">
          Get your music on Spotify, Apple Music, YouTube Music, Deezer, Pandora, TikTok, and more. Every month we send you a payment for your earnings.
        </p>
        <p class="text-base font-semibold">
          Banking on your success — we only make money when you earn money. <span class="text-pink-500">Just 15%.</span>
        </p>
        <div class="mt-8">
          <a href="#pricing" class="inline-block px-8 py-3 rounded-full border border-black text-black font-semibold hover:bg-black hover:text-white transition">
            Learn More About Pricing <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  {{-- Pricing --}}
  @if(!empty($global['require_subscription']) && $global['require_subscription'] === true)
    <section id="pricing" class="bg-gray-50 py-24 px-6 md:px-12 lg:px-20 text-center">
      <h2 class="text-4xl font-extrabold text-black mb-2">{{ $homepage->pricing_title }}</h2>
      <p class="text-gray-500 mb-12">{{ $homepage->pricing_text }}</p>

      <div class="{{ $gridClass }}">
        @foreach($plans as $plan)
          <div class="bg-white rounded-2xl shadow p-10 hover:-translate-y-1 transition flex flex-col justify-between">
            <div>
              <h3 class="text-xl font-bold text-black mb-2">{{ $plan->name }}</h3>
              <p class="text-5xl font-extrabold text-black">{{ formatCurrency($plan->price) }}</p>
              <p class="uppercase text-gray-500 text-sm mb-2">/ {{ $plan->billing_cycle }}</p>

              @if(!empty($plan->commission))
                <p class="text-pink-500 font-semibold mb-6">+ {{ $plan->commission }} of your earnings</p>
              @endif

              <ul class="text-left space-y-2 text-gray-600 text-sm mb-8">
                @foreach($planFeatures[$plan->id] ?? [] as $feature)
                  <li class="flex items-center gap-2">
                    <i class="fas fa-check text-pink-500"></i>
                    <span>{{ $feature }}</span>
                  </li>
                @endforeach
              </ul>
            </div>

            <x-cta-button block :registration-link="route('register', ['plan' => $plan->name])" />
          </div>
        @endforeach
      </div>

      <p class="text-gray-400 text-sm mt-12">No hidden fees · No add‑ons · No complicated tiers</p>
    </section>
  @endif

  {{-- Features --}}
  <section class="bg-black text-white py-24 px-6 md:px-12 lg:px-20">
    <div class="max-w-7xl mx-auto text-center">
      <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
        {{ $homepage->features_title }}
      </h2>
      <p class="text-gray-400 italic mb-12">
        {{ $homepage->features_text }}
      </p>

      <div class="grid md:grid-cols-3 gap-8 text-left">
        @foreach($features as $feature)
          @if(!empty($feature['title']))
            <div class="bg-[#111111] p-8 rounded-xl shadow hover:-translate-y-1 transition">
              <div class="flex items-center gap-3 mb-4">
                <i class="fas {{ $feature['icon'] ?? 'fa-check' }} text-gray-400 text-xl"></i>
                <h3 class="text-lg font-semibold text-white">{{ $feature['title'] }}</h3>
              </div>
              <p class="text-gray-400 text-sm leading-relaxed">
                {{ $feature['text'] }}
              </p>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  </section>

  {{-- Featured Artists --}}
  <section id="artists" class="bg-white py-24 px-6 md:px-12 lg:px-20 text-center">
    <h2 class="text-4xl font-extrabold text-black mb-12">Featured Artists</h2>
    <div class="grid md:grid-cols-4 gap-6 max-w-7xl mx-auto">
      @foreach($artists as $artist)
        @if(!empty($artist['name']))
          <div class="relative group rounded-lg overflow-hidden">
            <img src="{{ !empty($artist['image']) ? asset($artist['image']) : asset('images/home/hero.jpg') }}" alt="{{ $artist['name'] }}" class="w-full h-[400px] object-cover">
            <div class="absolute inset-0 opacity-40 group-hover:opacity-30 transition-opacity duration-300"
                 style="background-color: {{ e($artist['overlay_color'] ?? '#000000') }};"></div>
            <div class="absolute bottom-6 left-6 text-left text-white z-10">
              <h3 class="font-bold text-lg">{{ $artist['name'] }}</h3>
              <p class="text-sm opacity-90">{{ $artist['genre'] ?? 'Independent Artist' }}</p>
            </div>
          </div>
        @endif
      @endforeach
    </div>
  </section>

  {{-- CTA --}}
  <section id="contact" class="bg-black text-center py-32 px-6">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-5xl md:text-6xl font-extrabold text-pink-500 mb-4 leading-tight">
        {{ $homepage->cta_title_1 }} <br>
        {{ $homepage->cta_title_2 }}
      </h2>

      <p class="text-gray-300 mb-8 text-lg max-w-xl mx-auto leading-relaxed">
        {{ $homepage->cta_text }}
      </p>

      <x-cta-button size="lg" apply-link="#contact" />
    </div>
  </section>
@endsection