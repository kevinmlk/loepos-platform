@props(['active' => false, 'type' => 'a'])

@if ($type == 'a')
    <a class="{{ $active ? 'bg-blue text-white' : ' hover:bg-light-gray'}} px-3 py-2 rounded-md flex content-center text-base font-semibold" aria-current="{{ request()->is('/') ? 'page' : 'false' }}" {{$attributes}}>{{ $slot }}</a>
@else
    <button class="px-3 py-2 rounded-md flex content-center text-base font-semibold hover:bg-light-gray cursor-pointer w-full" {{$attributes}}>{{ $slot }}</button>
@endif
