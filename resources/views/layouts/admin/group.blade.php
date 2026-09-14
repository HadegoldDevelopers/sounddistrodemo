<div x-data="{ open: {{ $open ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open"
            class="flex items-center justify-between w-full px-4 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition
                   {{ $open ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
        <span class="flex items-center">
            <i class="fa {{ $icon }} mr-3 text-gray-400 group-hover:text-indigo-700"></i>
            {{ $label }}
        </span>
        <i :class="open ? 'fa fa-chevron-up' : 'fa fa-chevron-down'"></i>
    </button>

    <div x-show="open" x-collapse class="pl-10 space-y-1">
        @foreach($items as $item)
            <a href="{{ route($item['route']) }}"
               class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition
                      {{ request()->routeIs($item['route']) ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>
