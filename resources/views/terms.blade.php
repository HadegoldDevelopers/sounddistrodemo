@extends('layouts.frontend')
@section('title', 'Terms & Conditions')

@section('content')
<div class="bg-gray-50 min-h-screen">
  <!-- Hero Header -->
  <section class="bg-black text-center py-20 px-6">
    <h1 class="text-5xl md:text-6xl font-extrabold text-pink-500 mb-4">Terms & Conditions</h1>
    <p class="text-gray-300 text-lg">Last updated: {{ now()->format('F j, Y') }}</p>
  </section>

  <!-- Download Card -->
  <div class="max-w-3xl mx-auto px-6 py-16">
    <div class="bg-white rounded-xl border border-gray-200 shadow-lg p-10 text-center">
      <p class="text-gray-700 mb-6">
        You can download the full Terms & Conditions document below.
      </p>
      <a href="{{ asset($global['terms_pdf']) }}" download
         class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-pink-500 text-white font-semibold hover:bg-pink-600 transition">
        <span>Download Terms</span>
        <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>
</div>
@endsection
