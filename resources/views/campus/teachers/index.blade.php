@extends('campus.shared.layout')

@section('title', __('Professors'))
@section('subtitle', __('Gestió del professorat'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.dashboard')
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('Professors') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Professors</h1>
        @can('campus.teachers.create')
            <a href="{{ route('campus.teachers.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Nou Professor
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="mb-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="search" 
                                   placeholder="{{ __('campus.search_teacher') }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="clearSearch" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200 hidden">
                            <i class="bi bi-x-circle mr-2"></i>{{ __('campus.clear') }}
                        </button>
                        <span id="searchResults" class="text-sm text-gray-500 py-2"></span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Telèfon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cursos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Accions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($teachers as $teacher)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $teacher->fiscal_id ?? 'Sense DNI' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $teacher->email }}</div>
                                    @if($teacher->user)
                                        <div class="text-xs text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Actiu
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $teacher->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $teacher->courses->count() }} cursos
                                    </div>
                                    @if($teacher->courses->count() > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ $teacher->courses->sum('pivot.hours_assigned') }} h totals
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @can('campus.teachers.view')
                                            <a href="{{ route('campus.teachers.show', $teacher) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Veure">
                                                <i class="bi bi-eye"></i> 
                                            </a>
                                        @endcan
                                        @can('campus.teachers.edit')
                                            <a href="{{ route('campus.teachers.edit', $teacher) }}" 
                                               class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                                <i class="bi bi-pencil"></i> 
                                            </a>
                                        @endcan
                                        @can('campus.teachers.delete')
                                            <form action="{{ route('campus.teachers.destroy', $teacher) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Estàs segur que vols eliminar aquest professor?');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900" 
                                                        title="Eliminar">
                                                    <i class="bi bi-trash"></i> 
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No s'han trobat professors.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const clearButton = document.getElementById('clearSearch');
    const resultsSpan = document.getElementById('searchResults');
    const rows = document.querySelectorAll('tbody tr');
    const totalRows = rows.length;
    
    function updateSearchDisplay() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });
        
        // Update results counter
        if (searchTerm) {
            resultsSpan.textContent = `${visibleCount} / ${totalRows} {{ __('campus.results') }}`;
            clearButton.classList.remove('hidden');
        } else {
            resultsSpan.textContent = '';
            clearButton.classList.add('hidden');
        }
    }
    
    searchInput.addEventListener('input', updateSearchDisplay);
    
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        updateSearchDisplay();
        searchInput.focus();
    });
    
    // Initial display
    updateSearchDisplay();
});
</script>
@endsection
