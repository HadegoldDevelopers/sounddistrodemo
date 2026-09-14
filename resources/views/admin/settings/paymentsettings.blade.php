@extends('layouts.admin.app')

@section('title', 'Payment Gateways Settings')

@section('content')

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-800">Payment Gateways Settings</h1>

    {{-- ACRCloud Copyright Scanner (Phase 4) --}}
    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-200 mb-6">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-semibold text-gray-800">ACRCloud Copyright Scanner</h2>
            @if($scannerEnabled)
                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Enabled
                </span>
            @else
                <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-semibold px-2 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Disabled
                </span>
            @endif
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Every stitched master audio file is fingerprinted after upload. A match sets the track status to
            <strong>"blocked" (copyright detected)</strong> and flags the uploader's account
            (<code>users.flagged = true</code>).
        </p>

        @if(!$scannerEnabled)
            <p class="text-sm text-gray-600 mb-3">
                Add your free ACRCloud credentials (<a href="https://console.acrcloud.com" target="_blank" rel="noopener noreferrer" class="text-orange-600 hover:underline">free developer tier — 100 identifications/month</a>)
                to <code>.env</code> and restart the server to enable scanning:
            </p>
            <pre class="bg-gray-50 border border-gray-200 rounded p-3 text-xs overflow-x-auto">ACR_PROJECT_KEY=your_access_key
ACR_PROJECT_SECRET=your_access_secret
ACR_HOST={{ $acrHost }}</pre>
            <p class="text-xs text-gray-400 mt-2">
                Use the exact host from your ACRCloud project console (e.g. <code>identify-eu-west-1.acrcloud.com</code>).
            </p>
        @else
            <p class="text-sm text-green-700 mb-3">
                Scanner is live on <code>{{ $acrHost }}</code>. New uploads are fingerprinted automatically.
            </p>
        @endif

        <p class="text-xs text-gray-400 mt-2">
            Reference implementation: <code>App\Services\AudioScannerService</code> ·
            Alternative: ACRCloud native PHP SDK &rarr; <code>$acrcloud-&gt;identifyByFile($path)</code>
        </p>
    </div>

    <form method="POST" action="{{ route('admin.settings.payment-gateways.update') }}">
        @csrf
        @method('PUT')

        <div class="grid gap-6 md:grid-cols-2">
            @foreach($gateways as $gateway)
                <div class="bg-white rounded-lg shadow-sm p-5 flex flex-col border border-gray-200">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ $gateway->display_name }}</h2>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="gateways[{{ $gateway->name }}][enabled]" value="1" 
                                   {{ $gateway->enabled ? 'checked' : '' }} 
                                   class="form-checkbox text-orange-600">
                            <span class="text-gray-700">Enabled</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label for="mode_{{ $gateway->name }}" class="block mb-1 text-sm font-medium text-gray-700">Mode</label>
                        <select name="gateways[{{ $gateway->name }}][mode]" id="mode_{{ $gateway->name }}" 
                                class="w-full rounded border border-gray-300 p-2 text-gray-800 bg-white">
                            <option value="sandbox" {{ $gateway->mode === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="live" {{ $gateway->mode === 'live' ? 'selected' : '' }}>Live</option>
                        </select>
                    </div>
<div class="mb-4">
    <label class="block mb-1 text-sm font-medium text-gray-700">Note</label>
    <input type="text"
           name="gateways[{{ $gateway->name }}][note]"
           value="{{ old("gateways.{$gateway->name}.note", $gateway->note) }}"
           placeholder="e.g. Nigeria - NGN"
           class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
</div>

                    @php
                        $settings = $gateway->settings ?? [];
                    @endphp

                    @if($settings)
                        @foreach($settings as $key => $value)
                            <div class="mb-4">
                                <label for="{{ $gateway->name }}_{{ $key }}" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </label>
                                <input type="text" name="gateways[{{ $gateway->name }}][{{ $key }}]" 
                                       id="{{ $gateway->name }}_{{ $key }}" 
                                       value="{{ old("gateways.{$gateway->name}.{$key}", $value) }}"
                                       class="w-full rounded border border-gray-300 p-2 text-gray-800 bg-white">
                            </div>
                        @endforeach
                    @else
                    
                        @if($gateway->name === 'paystack')
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Public Key</label>
                                <input type="text" name="gateways[paystack][public_key]" value="{{ old('gateways.paystack.public_key') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Secret Key</label>
                                <input type="text" name="gateways[paystack][secret_key]" value="{{ old('gateways.paystack.secret_key') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                        @elseif($gateway->name === 'paypal')
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Client ID</label>
                                <input type="text" name="gateways[paypal][client_id]" value="{{ old('gateways.paypal.client_id') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Client Secret</label>
                                <input type="text" name="gateways[paypal][client_secret]" value="{{ old('gateways.paypal.client_secret') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                        @elseif($gateway->name === 'nowpayment')
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">API Key</label>
                                <input type="text" name="gateways[nowpayment][api_key]" value="{{ old('gateways.nowpayment.api_key') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                        @elseif($gateway->name === 'coinpayments')
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Public Key</label>
                                <input type="text" name="gateways[coinpayments][public_key]" value="{{ old('gateways.coinpayments.public_key') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Private Key</label>
                                <input type="text" name="gateways[coinpayments][private_key]" value="{{ old('gateways.coinpayments.private_key') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Merchant ID</label>
                                <input type="text" name="gateways[coinpayments][merchant_id]" value="{{ old('gateways.coinpayments.merchant_id') }}" class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
                            </div>
                        @elseif($gateway->name === 'moneyunify')
    <div class="mb-4">
        <label class="block mb-1 text-sm font-medium text-gray-700">Auth ID</label>
        <input type="text" 
               name="gateways[moneyunify][auth_id]" 
               value="{{ old('gateways.moneyunify.auth_id') }}" 
               class="w-full rounded border border-gray-300 p-2 bg-white text-gray-800">
    </div>
@endif

                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 rounded text-white font-semibold shadow">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
