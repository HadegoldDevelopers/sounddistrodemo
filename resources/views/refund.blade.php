@extends('layouts.frontend')
@section('title', 'Refund Policy')

@section('content')
<div class="bg-gray-50 min-h-screen">
  <!-- Hero Header -->
  <section class="bg-black text-center py-20 px-6">
    <h1 class="text-5xl md:text-6xl font-extrabold text-pink-500 mb-4">Refund Policy</h1>
    <p class="text-gray-300 text-lg">Last updated: {{ now()->format('F j, Y') }}</p>
  </section>

  <!-- Policy Content -->
  <div class="max-w-4xl mx-auto px-6 py-16">
    <div class="prose prose-gray max-w-none bg-white rounded-xl border border-gray-200 shadow-lg p-10">
      {!! $refund ?: '<p class="text-gray-500">Refund policy not yet published.</p>' !!}
    </div>
  </div>

  <!-- CTA Footer -->
  <section class="bg-pink-500 text-center py-16">
    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Questions About Refunds?</h2>
    <p class="text-white mb-6">Our support team is here to help you understand our refund process.</p>
    <a href="{{ route('home') }}#contact"
       class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-white text-pink-600 font-semibold hover:bg-gray-100 transition">
      <span>Contact Support</span>
      <i class="fas fa-arrow-right"></i>
    </a>
  </section>
</div>
@endsection
