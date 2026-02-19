@props(['href' => null, 'type' => 'button', 'size' => 'base'])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'base' => 'px-4 py-2 text-xs',
        'lg' => 'px-6 py-3 text-base',
    ][$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center $sizeClasses bg-red-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center $sizeClasses bg-red-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>
        {{ $slot }}
    </button>
@endif