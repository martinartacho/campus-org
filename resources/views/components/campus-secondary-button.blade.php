@props(['href' => null, 'type' => 'button', 'size' => 'base'])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'base' => 'px-4 py-2 text-xs',
        'lg' => 'px-6 py-3 text-base',
    ][$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center $sizeClasses bg-gray-200 border border-transparent rounded-md font-semibold text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center $sizeClasses bg-gray-200 border border-transparent rounded-md font-semibold text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"]) }}>
        {{ $slot }}
    </button>
@endif