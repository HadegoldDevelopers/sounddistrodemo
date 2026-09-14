@extends('layouts.user')
@section('title', 'Subscription')

@section('content')

<div class="max-w-6xl mx-auto py-10 px-6 grid md:grid-cols-2 gap-8">

    {{-- Subscription Status --}}
    <div class="mb-6 p-6 bg-zinc-900 rounded border border-zinc-700 col-span-2">
        <h2 class="text-3xl font-bold text-white mb-3">
            Subscription Status
        </h2>

        {{-- Case 1: Subscription NOT required --}}
        @if(!$requireSubscription)

            <p class="text-green-400 font-semibold text-lg">
                You are currently on the Free Plan
            </p>

        {{-- Case 2: User has active subscription --}}
        @elseif($currentPlan)

            <h3 class="text-white text-xl font-bold mb-1">
                Your Current Subscription
            </h3>

            <p class="text-orange-500 font-semibold text-lg">
                {{ ucfirst($currentPlan->role) }} Plan
            </p>

            <p class="text-white text-xl font-bold">
                {{ formatCurrency($currentPlan->price) }}
                <span class="text-zinc-400 text-sm ml-1">
                    {{ ucfirst($currentPlan->billing_cycle) }}
                </span>
            </p>

        {{-- Case 3: Subscription required but user not subscribed --}}
        @else

            <p class="text-red-400 font-semibold text-lg">
                No active subscription
            </p>

            <p class="text-zinc-400 text-sm">
                Please subscribe to distribute your music.
            </p>

        @endif
    </div>


    {{-- Upgrade Promotion --}}
    @if($requireSubscription && $upgradePlan && !request()->has('new_plan_id'))

        <div class="mb-6 p-5 bg-orange-700 bg-opacity-20 rounded border border-orange-500 col-span-2">

            <h3 class="text-xl font-bold text-white mb-2">
                Upgrade to Label Plan
            </h3>

            <p class="text-zinc-300 text-sm mb-3">
                Unlock advanced promotional tools and boost your exposure instantly.
            </p>

            <p class="text-orange-400 font-semibold mb-4">
                {{ formatCurrency($upgradePlan->price) }}
                / {{ ucfirst($upgradePlan->billing_cycle) }}
            </p>

            <form method="GET" action="{{ route('payment.index') }}">
                <input type="hidden" name="new_plan_id" value="{{ $upgradePlan->id }}">

                <button type="submit"
                    class="bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded font-semibold text-sm transition">
                    Upgrade Now
                </button>
            </form>

        </div>

    @endif


    {{-- Payment Methods --}}
    @if($requireSubscription && (!$user->is_sub || request()->has('new_plan_id')))

        <div class="col-span-2 md:col-span-1">

            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4">
                Choose Your Payment Method
            </h2>

            <form method="POST" action="{{ route('payment.process') }}" class="space-y-4">

                @csrf

                <input type="hidden"
                       name="subscription_plan_id"
                       value="{{ request('new_plan_id') ?? ($currentPlan->id ?? '') }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    @foreach ($paymentMethods as $gateway)

    <label class="flex flex-col p-3 bg-zinc-800 rounded cursor-pointer hover:bg-zinc-700">

        <div class="flex items-center">
            <input type="radio"
                   name="payment_method"
                   value="{{ $gateway['name'] }}"
                   class="mr-3"
                   required>

            <span class="text-white font-semibold text-sm sm:text-base">
                {{ $gateway['label'] }}
            </span>
        </div>

        @if (!empty($gateway['note']))
            <span class="text-xs text-zinc-400 mt-1">
                {{ $gateway['note'] }}
            </span>
        @endif

    </label>

@endforeach


                </div>
{{-- MoneyUnify Phone Number Field --}}
<div id="moneyunify-phone-field" class="hidden mt-4">
    <label class="block text-sm font-medium text-zinc-300 mb-1">
        Mobile Money Number
    </label>

    <input type="text"
           name="moneyunify_phone"
           placeholder="2609xxxxxxxx"
           class="w-full bg-zinc-800 border border-zinc-700 rounded p-3 text-white placeholder-zinc-500 focus:ring-orange-600 focus:border-orange-600">
</div>

                <button type="submit"
                    class="bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 sm:px-6 rounded font-semibold transition">
                    Continue to Payment
                </button>

            </form>

        </div>

    @endif



    {{-- Subscription History --}}
    @if($deposits->count())

        <div class="col-span-2 mt-8 bg-zinc-900 border border-zinc-800 rounded-xl p-6">

            <h3 class="text-lg font-semibold text-white mb-4">
                Subscription History
            </h3>


            {{-- Mobile Cards --}}
            <div class="space-y-4 md:hidden">

                @foreach($deposits as $deposit)

                    <div class="bg-zinc-800 rounded-lg p-4">

                        <p class="text-white font-semibold">
                            {{ $deposit->subscription_plan->name ?? 'N/A' }}
                        </p>

                        <p class="text-sm text-gray-400 mt-1">
                            Gateway: {{ strtoupper($deposit->gateway) }}
                        </p>

                        <p class="text-sm text-gray-400">
                            {{ $deposit->created_at->format('M d, Y') }}
                        </p>

                        <div class="flex justify-between items-center mt-3">

                            <span class="text-green-400 font-semibold">
                                {{ formatCurrency($deposit->original_amount, $deposit->original_currency) }}
                            </span>

                            @php $status = $deposit->status @endphp

                            <span class="text-xs px-2 py-1 rounded-full text-white
                                {{ $status === 'completed' || $status === 'paid'
                                    ? 'bg-green-600'
                                    : ($status === 'pending'
                                        ? 'bg-yellow-600'
                                        : 'bg-red-600') }}">

                                {{ ucfirst($status) }}

                            </span>

                        </div>

                    </div>

                @endforeach

            </div>



            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">

                <table class="w-full text-sm text-left text-gray-400">

                    <thead class="text-xs uppercase bg-zinc-800 text-gray-300">
                        <tr>
                            <th class="px-4 py-2">Plan</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Gateway</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($deposits as $deposit)

                            <tr class="border-b border-zinc-800 hover:bg-zinc-800/50">

                                <td class="px-4 py-3 text-white font-medium">
                                    {{ $deposit->subscription_plan->name ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3 text-green-400 font-semibold">
                                    {{ formatCurrency($deposit->original_amount, $deposit->original_currency) }}
                                </td>

                                <td class="px-4 py-3 uppercase">
                                    {{ $deposit->gateway }}
                                </td>

                                <td class="px-4 py-3">

                                    <span class="text-xs px-2 py-1 rounded-full text-white
                                        {{ $deposit->status === 'completed' || $deposit->status === 'paid'
                                            ? 'bg-green-600'
                                            : ($deposit->status === 'pending'
                                                ? 'bg-yellow-600'
                                                : 'bg-red-600') }}">

                                        {{ ucfirst($deposit->status) }}

                                    </span>

                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $deposit->created_at->format('M d, Y') }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($deposits->hasPages())

                <div class="mt-4">
                    <x-pager :__pager="$deposits" />
                </div>

            @endif

        </div>

    @endif

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[name="payment_method"]');
    const phoneField = document.getElementById('moneyunify-phone-field');

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === 'moneyunify') {
                phoneField.classList.remove('hidden');
            } else {
                phoneField.classList.add('hidden');
            }
        });
    });
});
</script>

@endsection