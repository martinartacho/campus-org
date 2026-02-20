{{-- resources/views/campus/students/courses.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('Els meus Cursos'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Els meus Cursos') }}</h1>
        <p class="text-gray-600">{{ __('Llista de cursos matriculats') }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-800">{{ __('Cursos actuals') }}</h2>
                    @if($season)
                        <p class="text-sm text-gray-600">{{ __('Temporada') }}: {{ $season->name }}</p>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="searchStudentCourse" 
                           placeholder="{{ __('campus.search_course') }}" 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        
        @if($currentRegistrations->isEmpty())
            <div class="p-8 text-center">
                <div class="mx-auto w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-book text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">{{ __('No tens cursos aquesta temporada') }}</h3>
                <p class="text-gray-500 mb-4">
                    {{ __('Consulta el catàleg de cursos i matricula\'t per començar a aprendre.') }}
                </p>
                <a href="{{ route('campus.courses.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="bi bi-compass me-2"></i>
                    {{ __('Explorar cursos') }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Curs') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Categoria') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Dates') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Estat') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Accions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($currentRegistrations as $registration)
                            <tr class="hover:bg-gray-50 student-course-row">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $registration->course->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $registration->course->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($registration->course->category)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                            {{ $registration->course->category->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $registration->course->start_date->format('d/m/Y') }} - 
                                    {{ $registration->course->end_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => __('Pendent'),
                                            'confirmed' => __('Confirmat'),
                                            'completed' => __('Completat'),
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$registration->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$registration->status] ?? $registration->status }}
                                    </span>
                                    @if($registration->grade)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ __('Nota') }}: {{ $registration->grade }}/10
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('campus.courses.show', $registration->course) }}" 
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="{{ __('Veure curs') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchStudentCourse');
    const rows = document.querySelectorAll('.student-course-row');
    
    function updateStudentCourseSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
        });
        
        // Show/hide no results message
        const noResultsMsg = document.getElementById('noStudentCourseResults');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        if (searchTerm && visibleRows.length === 0) {
            const msg = document.createElement('div');
            msg.id = 'noStudentCourseResults';
            msg.className = 'p-8 text-center text-gray-500';
            msg.innerHTML = `
                <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">{{ __('campus.no_results') }}</h3>
                <p class="text-gray-500">{{ __('campus.no_course_results') }}</p>
            `;
            searchInput.closest('.bg-white').querySelector('.overflow-x-auto').appendChild(msg);
        }
    }
    
    searchInput.addEventListener('input', updateStudentCourseSearch);
});
</script>
@endsection