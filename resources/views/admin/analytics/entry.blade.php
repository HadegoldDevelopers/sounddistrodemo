@extends('layouts.admin.app')
@section('title', 'Manual Stream Entry')

@section('content')

<div class="w-full max-w-3xl mx-auto px-4 py-8 space-y-6">

    <div>
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Manual Stream Entry</h2>
        <p class="text-sm text-gray-600 mt-1">Manually add streaming data for a track across stores.</p>
    </div>

    <form action="{{ route('admin.analytics.manual.store') }}" method="POST"
          class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf

        {{-- Track + Period --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Track <span class="text-red-500">*</span>
                </label>
                <select name="music_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">— Select Track —</option>
                    @foreach ($tracks as $track)
                        <option value="{{ $track->id }}" {{ old('music_id') == $track->id ? 'selected' : '' }}>
                            {{ $track->title }} — {{ $track->artistModel?->name ?? $track->artist ?? 'Unknown Artist' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                <input type="number" name="year" required
                       value="{{ old('year', now()->year) }}"
                       min="2000" max="{{ now()->year + 1 }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Month <span class="text-red-500">*</span></label>
                <select name="month" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $name)
                        <option value="{{ $i + 1 }}" {{ old('month', now()->month) == ($i + 1) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <input type="text" name="country"
                       value="{{ old('country', 'Global') }}"
                       placeholder="e.g. Nigeria, Global"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <p class="text-xs text-gray-400 mt-1">Applied to all stores below.</p>
            </div>

        </div>

        {{-- Store Rows --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Streams by Store
                <span class="text-xs text-gray-400 font-normal ml-1">— leave blank to skip a store</span>
            </label>

           @php
    $storeConfig = config('stores');
    $presetStores = array_keys($storeConfig);
@endphp

            {{-- SINGLE UNIFIED LIST — works on all screen sizes --}}
            <div class="rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden" id="storeList">

                @foreach ($presetStores as $store)
                    @php
                        $cfg = $storeConfig[$store] ?? [
                            'icon'  => 'default.svg',
                            'color' => '#6366f1',
                        ];
                        $bg = $cfg['color'] . '20';
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">

                        {{-- Icon + Name --}}
                        <div class="flex items-center gap-3 sm:w-44 flex-shrink-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                 style="background-color: {{ $bg }};">
                                <img src="{{ asset('images/icons/' . $cfg['icon']) }}"
                                     class="h-5 w-5 object-contain"
                                     alt="{{ $store }}">
                            </div>
                            <input type="hidden" name="stores[]" value="{{ $store }}">
                            <span class="font-medium text-gray-800 text-sm">{{ $store }}</span>
                        </div>

                        {{-- Inputs --}}
                        <div class="grid grid-cols-2 gap-3 flex-1">
                            <input type="number" name="streams[]" min="0" placeholder="Streams"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                    </div>
                @endforeach

            </div>

            {{-- Add custom store --}}
            <button type="button" onclick="addCustomRow()"
                    class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                + Add Another Store
            </button>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Save Stats
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function addCustomRow() {
    const row = `
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
            <div class="flex items-center gap-3 sm:w-44 flex-shrink-0">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-indigo-50 text-lg">
                    🎵
                </div>
                <input type="text" name="stores[]"
                       placeholder="Store name"
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3 flex-1">
                <input type="number" name="streams[]" min="0" placeholder="Streams"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </div>
    `;
    document.getElementById('storeList').insertAdjacentHTML('beforeend', row);
}
</script>
@endpush