@extends('campus.shared.layout')

@section('title', __('campus.teachers'))
@section('subtitle', $course->title)

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.courses.index') }}"
               class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.courses') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.teachers') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Asignar profesor --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">
            {{ __('campus.assign_teacher') }}
        </h2>

    {{-- Profesores asignados --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">
            {{ __('campus.assigned_teachers') }} ({{ $assignedTeachers->count() }})
        </h2>

        @forelse($assignedTeachers as $teacher)
            <div class="justify-left items-center border-b py-2">
                <div>
                    <strong>{{ $teacher->last_name }}, {{ $teacher->first_name }}</strong>
                </div>
                
                <div class="text-sm text-gray-500">
                         {{ __('campus.role') }}
                    <strong> {{ __('campus.teacher_role.' . $teacher->pivot->role) ?? '—' }}</strong>
                </div>

                <div class="text-sm text-gray-500">
                    {{ __('campus.hours') }}
                    <strong>{{ $teacher->pivot->hours_assigned }}</strong>
                </div>

                <form method="POST"
                      action="{{ route('campus.courses.teachers.destroy', [$course, $teacher]) }}"
                      onsubmit="return confirm('{{ __('campus.confirm_remove_teacher') }}')">
                    @csrf
                    @method('DELETE')

                    <button class="text-red-600 hover:text-red-900">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        @empty
            <p class="text-gray-500">
                {{ __('campus.no_teachers_assigned') }}
            </p>
        @endforelse
    </div>

    @php
        $assignedHours = $assignedTeachers->sum(fn($t) => $t->pivot->hours_assigned);
        $remainingHours = $course->hours - $assignedHours;
    @endphp

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">
            {{ __('campus.add_teacher') }}
        </h2>

        <p class="text-sm text-gray-500 mb-4">
            {{ __('campus.course_hours_summary', [
                'total' => $course->hours,
                'assigned' => $assignedHours,
                'remaining' => $remainingHours,
            ]) }}
        </p>

        <form method="POST"
            action="{{ route('campus.courses.teachers.store', $course) }}"
            class="flex gap-4 items-end">
            @csrf

            <div>
                <label class="campus-label">{{ __('campus.teacher') }}</label>
                <select name="teacher_id" class="campus-input" required>
                    <option value="">—</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                            {{ $teacher->last_name }}, {{ $teacher->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="campus-label">{{ __('campus.role') }}</label>
                <select name="role" class="campus-input" required>
                    @foreach(\App\Models\CampusCourse::TEACHER_ROLES as $key => $label)
                        <option value="{{ $key }}">
                            {{ __( 'campus.teacher_role.'.$key) }}
                        </option>
                    @endforeach
                </select>                
            </div>

            <div>
                <label class="campus-label">{{ __('campus.hours') }}</label>
                <input type="number"
                    name="hours_assigned"
                    class="campus-input w-24"
                    min="1"
                    max="{{ $remainingHours }}"
                    required>
            </div>

            <x-campus-button type="submit" variant="header">
                <i class="bi bi-check-circle me-2"></i>
                {{ __('campus.add') }}
            </x-campus-button>
        </form>
    </div>


    {{-- <div class="flex justify-between items-center border-b py-2">
         <div>  
        <h2 class="text-lg font-semibold mb-4">
            {{ __('campus.add_teacher') }}
        </h2>
        <form method="POST" action="{{ route('campus.courses.teachers.store', $course) }}" class="flex gap-4 items-end">
            @csrf
           
            <div >
                <label class="campus-label">{{ __('campus.teacher') }}</label>
                <select name="teacher_id" class="campus-input w-full" required>
                    <option value="">—</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                            {{ $teacher->last_name }}, {{ $teacher->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="campus-label">{{ __('campus.role') }}</label>
                <input type="text" name="role" class="campus-input">
            </div>

            <div>
                <label class="campus-label">{{ __('campus.hours') }}</label>
                <input type="number" name="hours_assigned" class="campus-input w-24">
            </div>

            
                <x-campus-button type="submit" variant="header" >
                    <i class="bi bi-check-circle me-2"></i>
                    {{ __('campus.add') }}
                </x-campus-button>
            </form>
        </div>

    </div> --}}

   
</div>
@endsection
