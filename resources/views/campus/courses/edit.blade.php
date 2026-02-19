{{-- resources/views/campus/courses/edit.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.edit_course'))
@section('subtitle', __('campus.edit_course'))

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
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.edit') }}</span>
        </div>
    </li>
@endsection

@section('content')
<h1 class="text-2xl font-bold mb-6">
    {{ __('campus.edit_course') }}
</h1>

<form method="POST"
      action="{{ route('campus.courses.update', $course) }}"
      class="space-y-6 max-w-3xl">
    @csrf
    @method('PUT')

    @include('campus.courses.partials.form', ['course' => $course])

    <div class="flex justify-end gap-2">
        <a href="{{ route('campus.courses.show', $course) }}"
           class="campus-secondary-button">
            {{ __('campus.cancel') }}
        </a>

        <button type="submit" class="campus-primary-button">
            {{ __('campus.update') }}
        </button>
    </div>
</form>
@endsection
