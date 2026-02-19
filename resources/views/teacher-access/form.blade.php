@extends('campus.shared.layout')

@section('title', __('campus.cobrament'))
@section('subtitle', __('campusAccés a la zona de cobrament'))

@section('content')

<div class="container mx-auto py-8">
    <!-- Router: Incluir el formulario correspondiente según el propósito -->
    @if(isset($purpose) && $purpose === 'consent') 
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-medium">Error: Aquesta funció és obsoleta</p>
            <p class="text-sm">Si us plau, comunica  aqueste fet a l'administració.</p>
        </div>
        {{-- @include('teacher-access.form-consent')  --}}
    @elseif(isset($purpose) && $purpose === 'payments')
        @include('teacher-access.form-payments')
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-medium">Error: Propòsit no especificat</p>
            <p class="text-sm">Si us plau, verifica l'enllaç d'accés o contacta amb l'administració.</p>
        </div>
    @endif
</div>

@endsection
