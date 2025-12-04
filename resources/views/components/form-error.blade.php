@props(['name'])

@error($name)
<p class="font-semibold text-rose-900 text-xs">{{$message}}</p>
@enderror