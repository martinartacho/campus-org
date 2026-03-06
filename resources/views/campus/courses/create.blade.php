{{-- resources/views/campus/couses/create.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.new_course'))
@section('subtitle', __('campus.new_course'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.courses.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.courses') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.new_course') }}</span>
        </div>
    </li>
@endsection

@section('content')
<h1 class="text-2xl font-bold mb-6">
    {{ __('campus.new_course') }}
</h1>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <h3 class="text-blue-800 font-semibold mb-2">📋 Nou Sistema de Codis</h3>
    <ul class="text-blue-700 space-y-1 text-sm">
        <li>• <strong>Curs Base:</strong> Plantilla amb codi únic (ex: BASE-ART-001)</li>
        <li>• <strong>Curs Impartit:</strong> Instància específica (ex: BASE-ART-001-202526-MAT)</li>
        <li>• <strong>Traçabilitat:</strong> Cada impartit es vincula al seu curs base</li>
        <li>• <strong>Cerca:</strong> Pots cercar per qualsevol tipus de codi</li>
    </ul>
</div>

<form method="POST" action="{{ route('campus.courses.store') }}"
      class="space-y-6 max-w-3xl">
    @csrf

    @include('campus.courses.partials.form')

    <div class="flex justify-end gap-2">
        <a href="{{ route('campus.courses.index') }}"
           class="campus-secondary-button">
            {{ __('campus.cancel') }}
        </a>

        <button type="submit" class="campus-primary-button">
            {{ __('campus.save') }}
        </button>
    </div>
</form>
@endsection
