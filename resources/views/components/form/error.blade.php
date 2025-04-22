@props(['name'])

@error($name)
  <span class="text-red text-overline font-medium">{{ $message }}</p>
@enderror
