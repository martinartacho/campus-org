@props([
    'icon' => 'bi-info-circle',
    'color' => 'blue',
    'label',
    'value' => 0,
])

@php
    $colors = [
        'blue'   => 'bg-blue-100 text-blue-600',
        'green'  => 'bg-green-100 text-green-600',
        'purple' => 'bg-purple-100 text-purple-600',
        'yellow' => 'bg-yellow-100 text-yellow-600',
        'red'    => 'bg-red-100 text-red-600',
    ];
@endphp

<div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
    <div class="flex items-center">
        <div class="flex-shrink-0 p-3 rounded-md {{ $colors[$color] ?? $colors['blue'] }}">
            <i class="bi {{ $icon }} text-xl"></i>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">
                {{ $label }}
            </p>
            <p class="text-2xl font-semibold text-gray-900">
                {{ $value }}
            </p>
        </div>
    </div>
</div>
