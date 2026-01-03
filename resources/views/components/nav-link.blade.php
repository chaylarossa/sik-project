@props(['active' => false])

@php
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2 rounded-md bg-indigo-50 text-indigo-700 font-semibold'
        : 'flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
