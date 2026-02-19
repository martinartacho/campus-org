@extends('campus.shared.layout')

@section('title', __('campus.enrolled_students'))
@section('subtitle', $course->title)

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teacher.courses.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.my_courses')
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.enrolled_students') }} - {{ $course->code }}
            </span>
        </div>
    </li>
@endsection

@section('actions')
    <div class="flex space-x-2">
        <a href="{{ route('campus.teacher.courses.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
            </svg>
            @lang('campus.my_courses')
        </a>
        
        @if($course->teachers->count() > 1)
            <button type="button" 
                    onclick="document.getElementById('teachers-modal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                <i class="bi bi-people-fill mr-2"></i>
                @lang('campus.other_teachers')
            </button>
        @endif
    </div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Contador de estudiantes --}}
    <div class="bg-blue-50 rounded-lg border border-blue-100 p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-blue-700">
                    @lang('campus.total_students')
                </div>
                <div class="text-3xl font-bold text-blue-800 mt-1">
                    {{ count($students) }}
                </div>
            </div>
            
            <div class="text-right">
                <div class="text-sm text-blue-600">
                    @if($course->max_students)
                        @lang('campus.available_spots', [
                            'available' => $course->max_students - count($students),
                            'total' => $course->max_students
                        ])
                    @else
                        @lang('campus.no_limit')
                    @endif
                </div>
                <div class="mt-2 text-xs text-blue-500">
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    @if(empty($students) || count($students) === 0)
        {{-- Sin estudiantes --}}
        <div class="text-center py-12 bg-white rounded-lg shadow-sm border">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-6.197a6 6 0 00-9-5.197" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">
                @lang('campus.no_students_enrolled')
            </h3>
            <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                Encara no hi ha estudiants matriculats en aquest curs.
            </p>
        </div>
    @else
        {{-- Lista de estudiantes --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
            <div class="p-6">
                {{-- Filtros y búsqueda --}}
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="search-students" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="@lang('campus.search_student')">
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="bi bi-download mr-2"></i>
                            @lang('campus.export')
                        </button>
                        
                        <button type="button" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="bi bi-envelope mr-2"></i>
                            @lang('campus.send_email')
                        </button>
                    </div>
                </div>

                {{-- Tabla de estudiantes --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang('campus.name')
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang('campus.code')
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang('campus.email')
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang('campus.registered_at')
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang('campus.status')
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang('campus.actions')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                                <tr class="student-row hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ strtoupper(substr($student['name'], 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 student-name">
                                                    {{ $student['name'] }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    ID: {{ $student['id'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">
                                            {{ $student['student_code'] }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="mailto:{{ $student['email'] }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $student['email'] }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($student['registration_date'])
                                            <div class="flex items-center">
                                                <i class="bi bi-calendar3 mr-2 text-gray-400"></i>
                                                {{ $student['registration_date']->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($student['status'] === 'confirmed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle mr-1"></i>
                                                @lang('campus.registration_status_confirmed')
                                            </span>
                                        @elseif($student['status'] === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="bi bi-check-circle-fill mr-1"></i>
                                                @lang('campus.registration_status_completed')
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="bi bi-clock mr-1"></i>
                                                {{ ucfirst($student['status']) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button type="button" 
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                                    title="@lang('campus.view')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            
                                            <a href="mailto:{{ $student['email'] }}" 
                                               class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50"
                                               title="@lang('campus.contact')">
                                                <i class="bi bi-envelope"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="text-purple-600 hover:text-purple-900 p-1 rounded hover:bg-purple-50"
                                                    title="@lang('campus.view_profile')">
                                                <i class="bi bi-person-lines-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Estadísticas al pie --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">
                            <i class="bi bi-pie-chart mr-2"></i>
                            @lang('campus.students_by_status')
                        </h4>
                        <div class="mt-2">
                            @php
                                $confirmedCount = collect($students)->where('status', 'confirmed')->count();
                                $completedCount = collect($students)->where('status', 'completed')->count();
                            @endphp
                            <div class="flex items-center justify-between text-sm mb-1">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                    <span>@lang('campus.registration_status_confirmed')</span>
                                </div>
                                <span class="font-medium">{{ $confirmedCount }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                    <span>@lang('campus.registration_status_completed')</span>
                                </div>
                                <span class="font-medium">{{ $completedCount }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">
                            <i class="bi bi-bar-chart mr-2"></i>
                            @lang('campus.attendance_summary')
                        </h4>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-gray-800">
                                {{ count($students) }} / {{ $course->max_students ?? '∞' }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                @if($course->max_students)
                                    @php
                                        $percentage = $course->max_students > 0 ? round((count($students) / $course->max_students) * 100, 1) : 0;
                                    @endphp
                                    {{ $percentage }}% @lang('campus.occupancy_rate')
                                @else
                                    @lang('campus.no_limit')
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">
                            <i class="bi bi-lightning-charge mr-2"></i>
                            @lang('campus.quick_actions')
                        </h4>
                        <div class="mt-2 space-y-2">
                            <button type="button" 
                                    class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="bi bi-download mr-2"></i>
                                @lang('campus.export_list')
                            </button>
                            <button type="button" 
                                    onclick="document.getElementById('notification-modal').classList.remove('hidden')"
                                    class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="bi bi-bell mr-2"></i>
                                @lang('campus.send_notification')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal de otros profesores --}}
@if($course->teachers->count() > 1)
<div id="teachers-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="bi bi-people-fill mr-2"></i>
                @lang('campus.other_teachers')
            </h3>
        </div>
        
        <div class="px-6 py-4">
            <div class="space-y-3">
                @foreach($course->teachers as $courseTeacher)
                    @if($courseTeacher->id !== $teacher->id)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium text-sm mr-3">
                                    {{ substr($courseTeacher->first_name, 0, 1) }}{{ substr($courseTeacher->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $courseTeacher->first_name }} {{ $courseTeacher->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $courseTeacher->teacher_code }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-700">
                                    {{ trans('campus.teacher_role.' . ($courseTeacher->pivot->role ?? 'assistant')) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $courseTeacher->pivot->hours_assigned ?? 0 }}h
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="px-6 py-4 border-t bg-gray-50 rounded-b-lg flex justify-end">
            <button type="button" 
                    onclick="document.getElementById('teachers-modal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                @lang('campus.close')
            </button>
        </div>
    </div>
</div>
@endif

{{-- Script para búsqueda --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-students');
    const studentRows = document.querySelectorAll('.student-row');
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        
        studentRows.forEach(row => {
            const studentName = row.querySelector('.student-name').textContent.toLowerCase();
            const studentCode = row.querySelector('code').textContent.toLowerCase();
            const studentEmail = row.querySelector('a[href^="mailto:"]').textContent.toLowerCase();
            
            if (studentName.includes(searchTerm) || 
                studentCode.includes(searchTerm) || 
                studentEmail.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection