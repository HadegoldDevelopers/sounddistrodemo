@extends('layouts.user')

@section('title', 'Manual Payment Instructions')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-zinc-900 rounded-lg shadow space-y-6 mt-8 text-white">

    <h2 class="text-2xl font-bold">
        Manual Payment Instructions
    </h2>

    <p class="text-gray-300">
        Please follow the instructions below to complete your payment.
        Once payment is sent, our team will verify it manually and activate your subscription.
    </p>

    {{-- PAYMENT DETAILS --}}
    <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-3">Payment Details</h3>

        <ul class="space-y-2 text-gray-300">
            <li><strong>Amount:</strong> {{ $amount }} {{ $currency }}</li>
            <li><strong>Plan:</strong> {{ $plan->name }}</li>
            <li><strong>Reference:</strong> {{ $reference }}</li>
        </ul>
    </div>

    {{-- INSTRUCTIONS --}}
    <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-3">How to Pay</h3>

        <p class="text-gray-300 mb-3">
            Send the exact amount to the account below:
        </p>

        <ul class="space-y-2 text-gray-300">
            <li><strong>Account Name:</strong> {{ $manual->account_name }}</li>
            <li><strong>Account Number:</strong> {{ $manual->account_number }}</li>
            <li><strong>Provider:</strong> {{ $manual->provider }}</li>

            @if(!empty($manual->extra_info))
                <li><strong>Extra Info:</strong> {{ $manual->extra_info }}</li>
            @endif
        </ul>
    </div>

    {{-- BACK BUTTON ONLY --}}
    <div class="flex">
        <a href="{{ route('payment.index') }}"
           class="w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded transition">
            Back
        </a>
    </div>

    <p class="text-center text-gray-500 text-sm">
        Our team will verify your payment shortly.
    </p>

</div>
@endsection
