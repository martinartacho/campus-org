@props(['title', 'color' => 'gray', 'link' => null])

<div class="bg-{{ $color }}-50 p-4 rounded-lg border-l-4 border-{{ $color }}-400">
    <h4 class="font-semibold text-{{ $color }}-800">{{ $title }}</h4>
    <div class="mt-2 text-gray-600">
        {{ $slot }}
    </div>
    @if($link)
        <a href="{{ $link }}" class="mt-3 inline-block text-{{ $color }}-600 hover:underline">
        {{ __('See details.') }} →
        </a>
    @endif
</div>