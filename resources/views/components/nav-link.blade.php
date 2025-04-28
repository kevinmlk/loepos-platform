@props(['active' => false, 'type' => 'a'])

@if ($type == 'a')

<a class="{{ $active ? 'bg-light-blue' : ' hover:bg-light-blue'}} px-3 py-2 rounded-md flex content-center text-base font-semibold ml-3" aria-current="{{ request()->is('/') ? 'page' : 'false' }}" {{$attributes}}>{{ $slot }}</a>

@else

<button class="px-3 py-2 rounded-md flex content-center text-base font-semibold ml-3 hover:bg-light-blue cursor-pointer" {{$attributes}}>{{ $slot }}</button>

@endif
