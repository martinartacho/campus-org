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
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">
                {{ __('campus.assigned_teachers') }} ({{ $assignedTeachers->count() }})
            </h2>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" 
                       id="searchTeacher" 
                       placeholder="{{ __('campus.search_teacher') }}" 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        @forelse($assignedTeachers as $teacher)
            <div class="teacher-item justify-left items-center border-b py-2">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTeacher');
    const teacherItems = document.querySelectorAll('.teacher-item');
    const totalItems = teacherItems.length;
    
    function updateTeacherSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        teacherItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            item.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });
        
        // Show/hide no results message
        const noResultsMsg = document.getElementById('noTeacherResults');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        if (searchTerm && visibleCount === 0) {
            const msg = document.createElement('div');
            msg.id = 'noTeacherResults';
            msg.className = 'text-center py-4 text-gray-500';
            msg.textContent = '{{ __('campus.no_results') }}';
            searchInput.closest('.bg-white').querySelector('.space-y-6').appendChild(msg);
        }
    }
    
    searchInput.addEventListener('input', updateTeacherSearch);
});
</script>
@endsection
