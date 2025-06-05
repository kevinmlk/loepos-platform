@props([
    'type' => 'primary', // 'primary', 'secondary', 'tertiary'
    'disabled' => false, // true or false
    'href' => null, // if set, render <a> instead of <button>
    'icon' => null, // e.g. 'phosphor-plus-bold'
    'iconPosition' => 'left', // 'left' or 'right'
])

@php
    $baseClasses = 'flex items-center rounded-xl px-6 h-12 text-button font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:cursor-pointer';

    $types = [
        'primary' => 'text-white bg-blue hover:bg-dark-blue focus:ring-blue-500',
        'secondary' => 'text-black bg-gray-200 hover:bg-gray-300 focus:ring-gray-400',
        'tertiary' => 'text-blue bg-transparent hover:bg-transparant-blue focus:ring-blue-200',
    ];

    $disabledClasses = 'opacity-50 cursor-not-allowed pointer-events-none';

    $iconClasses = 'w-6 h-6';

    $finalClasses = "$baseClasses {$types[$type]}" . ($disabled ? " $disabledClasses" : '');
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $finalClasses]) }}
    >
        @include('components.ui.button-content')
    </a>
@else
    <button
        {{ $attributes->merge(['class' => $finalClasses]) }}
        @if($disabled) disabled @endif
    >
        @include('components.ui.button-content')
    </button>
@endif
