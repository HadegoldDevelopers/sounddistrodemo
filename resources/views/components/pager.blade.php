@props(['__pager'])

@php
    $isAdmin = Auth::guard('admin')->check();

    $links = $__pager->linkCollection()->toArray();
    $bgColor = $isAdmin ? 'bg-indigo-600' : 'bg-orange-600';
    $textColor = $isAdmin ? 'text-indigo-600' : 'text-orange-600';
    $hoverColor = $isAdmin ? 'hover:bg-indigo-50' : 'hover:bg-orange-50';
@endphp

@if ($__pager->hasPages())
    <nav class="flex justify-center mt-4">
        <ul class="flex space-x-1">

            {{-- Previous --}}
            @if ($__pager->onFirstPage())
                <li class="px-3 py-1 text-gray-400">←</li>
            @else
                <li>
                    <a href="{{ $__pager->previousPageUrl() }}"
                       class="px-3 py-1 {{ $textColor }} {{ $hoverColor }} rounded">←</a>
                </li>
            @endif

            {{-- Page Links --}}
            @foreach ($links as $link)
                @if (!is_numeric($link['label']) && $link['label'] !== '...' && !ctype_digit($link['label']))
                    @continue
                @endif

                {{-- Ellipsis --}}
                @if ($link['label'] === '...')
                    <li class="px-3 py-1 text-gray-400">…</li>

                {{-- Active page --}}
                @elseif ($link['active'])
                    <li>
                        <span class="px-3 py-1 {{ $bgColor }} text-white rounded">
                            {{ $link['label'] }}
                        </span>
                    </li>

                {{-- Normal page --}}
                @else
                    <li>
                        <a href="{{ $link['url'] }}" class="px-3 py-1 {{ $textColor }} {{ $hoverColor }} rounded">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($__pager->hasMorePages())
                <li>
                    <a href="{{ $__pager->nextPageUrl() }}"
                       class="px-3 py-1 {{ $textColor }} {{ $hoverColor }} rounded">→</a>
                </li>
            @else
                <li class="px-3 py-1 text-gray-400">→</li>
            @endif

        </ul>
    </nav>
@endif
