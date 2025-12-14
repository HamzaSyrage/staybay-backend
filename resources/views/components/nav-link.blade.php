@props(['route'])
<a href="{{route($route)}}" {{$attributes->merge(['class'=>'inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white'])}}>
   {{$slot}}
</a>
