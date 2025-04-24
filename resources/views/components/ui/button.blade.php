@props([
  'type' => 'primary', // 'primary', 'secondary', 'tertiary'
  'disabled' => false, // true or false
  'icon' => null, // e.g. 'icon="phosphor-plus-bold"'
  'iconPosition' => 'left', // 'left' or 'right'
])

@php
  $baseClasses = 'inline-flex items-center rounded-xl px-6 py-3 text-button font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

  $types = [
    'primary' => 'text-white bg-blue hover:bg-dark-blue cursor-pointer focus:ring-blue-500',
    'secondary' => 'text-black bg-gray-200 hover:bg-gray-300 focus:ring-gray-400',
    'tertiary' => 'text-blue-600 bg-transparent hover:bg-blue-100 focus:ring-blue-200',
  ];

  $disabledClasses = 'opacity-50 cursor-not-allowed pointer-events-none';

  $iconClasses = 'w-4 h-4';
@endphp

<button
  {{ $attributes->merge([
    'class' => "$baseClasses {$types[$type]}" . ($disabled ? " $disabledClasses" : ''),
    'disabled' => $disabled,
  ]) }}
>
  {{-- Icon left --}}
  @if ($icon && $iconPosition === 'left')
    <x-dynamic-component :component="$icon" class="{{ $iconClasses }} mr-2" />
  @endif

  {{ $slot }}

  {{-- Icon right --}}
  @if ($icon && $iconPosition === 'right')
    <x-dynamic-component :component="$icon" class="{{ $iconClasses }} ml-2" />
  @endif
</button>
