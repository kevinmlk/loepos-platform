@props([
    'name',
    'tab',
])

@php
    $active = request('tab', 'overview') === $tab;
@endphp

<a
    href="{{ route(Route::currentRouteName(), ['tab' => $tab]) }}"
    class="px-3 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium {{ $active ? 'bg-blue text-white ' : 'text-black' }} hover:bg-blue hover:text-white focus:outline-none"
>
    {{ $name }}
</a>
