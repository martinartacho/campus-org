@props([
    'stats' => [],
    'error' => null,
])

@php
    $activeRole = session('active_role') ?? 'manager';
@endphp

{{-- Breadcrumbs --}}
@hasSection('breadcrumbs')
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="bi bi-house-door me-1"></i>
                    {{ __('Manager') }} - {{ ucfirst($activeRole) }} 
                </a>
            </li>
            @yield('breadcrumbs')
        </ol>
    </nav>
</div>
@endif


<div class="space-y-6">

    {{-- ERROR --}}
    @if($error)
        <div class="bg-red-100 text-red-800 p-4 rounded">
            {{ $error }}
        </div>
        @return
    @endif




</div>

