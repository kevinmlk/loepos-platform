{{-- resources/views/components/ui/card.blade.php --}}
@props(['class' => ''])
<article {{ $attributes->merge([
      'class' => 'rounded-xl border border-gray-200 bg-white shadow p-6 space-y-5 '.$class
]) }}>
    {{ $slot }}
</article>
