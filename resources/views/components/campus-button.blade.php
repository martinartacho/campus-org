@props([
    'href' => null,
    'variant' => null,   // puede venir o no
    'size' => 'base',
    'type' => 'button',
    'color' => null,     //  Si llegan los dos → gana variant 
])

@php
    /*
    |--------------------------------------------------------------------------
    | Sizes
    |--------------------------------------------------------------------------
    */
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'base' => 'px-4 py-2 text-xs',
        'lg' => 'px-6 py-3 text-base',
    ];

    /*
    |--------------------------------------------------------------------------
    | Color → Variant mapping (legacy support)
    |--------------------------------------------------------------------------
    */
    if (! $variant && $color) {
        $variant = match ($color) {
            'red'   => 'danger',
            'gray'  => 'secondary',
            'blue'  => 'primary',
            'green' => 'primary', // o crea "success" en el futuro
            default => 'primary',
        };
    }

    // Fallback final
    $variant = $variant ?? 'primary';

    /*
    |--------------------------------------------------------------------------
    | Variants
    |--------------------------------------------------------------------------
    */
    $variants = [
        'primary' => '
            bg-blue-600 text-white
            hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800
            focus:ring-blue-500
        ',

        'secondary' => '
            bg-gray-100 text-gray-800 border border-gray-300
            hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300
            focus:ring-gray-400
        ',

        'header' => '
            bg-white text-blue-600 border border-blue-600
            hover:bg-blue-50 active:bg-blue-100
            focus:ring-blue-500
        ',

        'danger' => '
            bg-red-600 text-white
            hover:bg-red-700 focus:bg-red-700 active:bg-red-800
            focus:ring-red-500
        ',
        'success' => '
            bg-green-500 text-white
            hover:bg-green-700 focus:bg-green-700 active:bg-green-800
            focus:ring-green-500
        ',
    ];

    /*
    |--------------------------------------------------------------------------
    | Base classes
    |--------------------------------------------------------------------------
    */
    $baseClasses = '
        inline-flex items-center justify-center
        rounded-md font-semibold uppercase tracking-widest
        transition ease-in-out duration-150
        focus:outline-none focus:ring-2 focus:ring-offset-2
    ';

    $classes = implode(' ', [
        $baseClasses,
        $sizes[$size] ?? $sizes['base'],
        $variants[$variant] ?? $variants['primary'],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </button>
@endif
