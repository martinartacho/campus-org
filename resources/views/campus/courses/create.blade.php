@extends('campus.shared.layout')

@section('title', $parentCourse ? 'Crear Instància' : __('campus.new_course'))
@section('subtitle', $parentCourse ? 'Instància de: ' . $parentCourse->title : __('campus.new_course'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.courses.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.courses') }}
            </a>
        </div>
    </li>
    @if($parentCourse)
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.courses.show', $parentCourse) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ $parentCourse->title }}
            </a>
        </div>
    </li>
    @endif
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ $parentCourse ? 'Crear Instància' : __('campus.new_course') }}</span>
        </div>
    </li>
@endsection

@section('content')
@if($parentCourse)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <i class="bi bi-info-circle text-blue-600 mr-2"></i>
            <div>
                <h3 class="text-sm font-medium text-blue-800">Creant instància del curs base</h3>
                <p class="text-sm text-blue-600 mt-1">
                    Curs pare: <strong>{{ $parentCourse->title }}</strong> ({{ $parentCourse->code }})
                </p>
            </div>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <h3 class="text-red-800 font-semibold mb-2">❌ Errors trobats:</h3>
        <ul class="text-red-700 space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <h3 class="text-green-800 font-semibold">✅ {{ session('success') }}</h3>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <h3 class="text-red-800 font-semibold">❌ {{ session('error') }}</h3>
    </div>
@endif

<form method="POST" action="{{ route('campus.courses.store') }}"
      class="space-y-6 max-w-3xl">
    @csrf
    
    @if($parentCourse)
        <input type="hidden" name="parent_id" value="{{ $parentCourse->id }}">
    @endif

    @include('campus.courses.partials.form', ['defaultData' => $defaultData ?? []])

    <div class="flex justify-end gap-2">
        <a href="{{ route('campus.courses.index') }}"
           class="campus-secondary-button">
            {{ __('campus.cancel') }}
        </a>

        <button type="submit" class="campus-primary-button">
            {{ $parentCourse ? 'Crear Instància' : __('campus.save') }}
        </button>
    </div>
</form>
@endsection
