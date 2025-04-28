{{-- resources/views/components/ui/button-content.blade.php --}}
@if ($icon && $iconPosition === 'left')
    <x-dynamic-component :component="$icon" class="{{ $iconClasses }} mr-2" />
@endif

{{ $slot }}

@if ($icon && $iconPosition === 'right')
    <x-dynamic-component :component="$icon" class="{{ $iconClasses }} ml-2" />
@endif
