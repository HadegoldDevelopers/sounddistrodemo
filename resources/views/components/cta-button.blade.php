@props([
    'registrationLink' => null,
    'applyLink' => null,
    'color' => 'pink',
    'size' => 'md',
    'block' => false,
])

<a href="{{ $global['allow_user_registration'] ? ($registrationLink ?: route('register')) : ($applyLink ?: route('home') . '#contact') }}"
   {{ $attributes->merge(['class' => trim(
       ($block ? 'block w-full text-center ' : 'inline-block ') .
       ($size === 'lg' ? 'px-8 py-4 ' : 'px-8 py-3 ') .
       'rounded-full font-semibold shadow-md transition ' .
       ($color === 'yellow' ? 'bg-yellow-300 text-black hover:bg-yellow-400' : 'bg-pink-500 text-white hover:bg-pink-600')
   )]) }}>
    {{ $global['allow_user_registration'] ? 'SIGN UP FREE' : 'APPLY NOW' }}
    <i class="fas fa-arrow-right"></i>
</a>