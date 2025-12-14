@props(['route'])
@if(isset($route))
    <form action="{{route($route)}}" {{$attributes}}>
        {{$slot}}
    </form>
@else
    <form {{$attributes}}>
        {{$slot}}
    </form>
@endif
