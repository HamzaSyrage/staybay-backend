{{-- @props(['active'=>false]) --}}
@php
    $active = request()->is(strlen($attributes['href'])>1?substr($attributes['href'],1):$attributes['href']);
@endphp

<a href="{{ $attributes['href'] }}"
aria-current="{{$active?'page':'false'}}"
class="{{$active?'bg-gray-950/50 text-white':'text-gray-300 hover:bg-white/5 hover:text-white'.$attributes['class']}}
rounded-md px-3 py-2 text-sm font-medium" >
{{$slot}}
</a>

{{--
@props(['type'=>'a'])
@php
    $active = request()->is(strlen($attributes['href'])>1?substr($attributes['href'],1):$attributes['href']);
@endphp
@if($type =='a')
<a href="{{ $attributes['href'] }}"
aria-current="{{$active?'page':'false'}}"
class="{{$active?'bg-gray-950/50 text-white':'text-gray-300 hover:bg-white/5 hover:text-white'}}
rounded-md px-3 py-2 text-sm font-medium" >
{{$slot}}
</a>

@else
<button
aria-current="{{$active?'page':'false'}}"
class="{{$active?'bg-gray-950/50 text-white':'text-gray-300 hover:bg-white/5 hover:text-white'}}
rounded-md px-3 py-2 text-sm font-medium" >
{{$slot}}
</button>
@endif





--}}
