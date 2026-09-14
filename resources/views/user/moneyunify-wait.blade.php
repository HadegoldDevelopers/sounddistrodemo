@extends('layouts.app')

@section('title', 'Processing Payment')

@section('content')
<div class="max-w-lg mx-auto mt-20 text-center">

    <h1 class="text-2xl font-bold text-white mb-4">Processing Your Payment</h1>

    <p class="text-zinc-400 mb-6">
        Please approve the Mobile Money prompt on your phone to complete the payment.
    </p>

    <div class="flex justify-center mb-6">
        <svg class="animate-spin h-10 w-10 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>

    <p class="text-sm text-zinc-500">
        Waiting for confirmation…
    </p>

</div>

<script>
    const checkUrl = "{{ route('subscription.moneyunify.check', $transactionId) }}";

    setInterval(() => {
        fetch(checkUrl)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = "{{ route('user.dashboard') }}";
                }

                if (data.status === 'failed') {
                    window.location.href = "{{ route('payment.index') }}";
                }
            });
    }, 5000);
</script>
@endsection
