@props(['active' => false, 'type' => 'a'])

@if ($type == 'a')
    <a class="{{ $active ? 'bg-blue text-white' : ' hover:bg-blue hover:text-white '}} px-3 py-2 rounded-md flex content-center text-base font-semibold" aria-current="{{ request()->is('/') ? 'page' : 'false' }}" {{$attributes}}>{{ $slot }}</a>
@else
    <button class="px-3 py-2 rounded-md flex content-center text-base font-semibold hover:bg-blue cursor-pointer w-full hover:text-white" {{$attributes}}>{{ $slot }}</button>
@endif
