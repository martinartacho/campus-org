{{-- resources/views/campus/courses/show.blade.php --}}
@extends('campus.shared.layout')

@section('title', $course->title)
@section('subtitle', $course->title)

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
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.course_details') }}</span>
        </div>
    </li>
@endsection



@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">
        {{ $course->title }}
    </h1>

    <div class="flex gap-2">
    @can('campus.courses.edit')
        <a href="{{ route('campus.courses.edit', $course) }}"
           class="campus-primary-button">
            {{ __('campus.edit') }}
        </a>
    @endcan

    @can('campus.teachers.assign')
        <a href="{{ route('campus.courses.teachers', $course) }}"
           class="campus-secondary-button">
            <i class="bi bi-person-badge me-1"></i>
            {{ __('campus.teachers') }}
        </a>
    @endcan

    @can('campus.registrations.view')
        <a href="{{ route('campus.courses.registrations', $course) }}"
        class="campus-secondary-button">
            <i class="bi bi-people me-1"></i>
            {{ __('campus.students') }}
        </a>
    @endcan

    
</div>
</div>

<div class="bg-white shadow rounded-lg p-6 space-y-4 max-w-3xl">
    <div>
        <strong>{{ __('campus.season') }}:</strong>
        {{ $course->season?->name ?? '—' }}
    </div>

    <div>
        <strong>{{ __('campus.category') }}:</strong>
        {{ $course->category?->name ?? '—' }}
    </div>

    <div>
        <strong>{{ __('campus.description') }}:</strong>
        <p class="mt-1 text-gray-700">
            {{ $course->description ?? '—' }}
        </p>
    </div>

    <div>
        <strong>{{ __('campus.status') }}:</strong>
        {{ $course->is_active ? __('campus.active') : __('campus.inactive') }}
    </div>
</div>
@endsection
