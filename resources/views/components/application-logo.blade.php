@php
    use App\Models\Setting;

    $logoPath = Setting::get('logo');
@endphp

@if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
    <img src="{{ asset('storage/' . $logoPath) }}" alt="Campus UPG"  class="h-10 max-h-12 w-auto object-contain">
@else
    <img src="{{ asset('img/logo.svg') }}" alt="Campus UPG"  class="h-10 max-h-12 w-auto object-contain">
@endif