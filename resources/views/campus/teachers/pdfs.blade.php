{{-- resources/views/campus/teachers/pdfs.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('PDFs de Teachers'))

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        {{-- Capçalera --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="bi bi-file-earmark-pdf mr-2"></i>
                    {{ __('PDFs de Teachers') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Llista de tots els PDFs generats pels teachers. Pots veure, descarregar o eliminar fitxers.') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('campus.teachers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Tornar a Teachers') }}
                </a>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Filtrar per estat') }}
                    </label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">{{ __('Tots') }}</option>
                        <option value="has_pdfs">{{ __('Amb PDFs') }}</option>
                        <option value="no_pdfs">{{ __('Sense PDFs') }}</option>
                        <option value="iban_ok">{{ __('IBAN Configurat') }}</option>
                        <option value="iban_missing">{{ __('IBAN No Configurat') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Cercar per nom') }}
                    </label>
                    <input type="text" id="nameFilter" placeholder="{{ __('Nom o cognom del teacher') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Ordenar per') }}
                    </label>
                    <select id="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="name">{{ __('Nom') }}</option>
                        <option value="pdf_count">{{ __('Nombre de PDFs') }}</option>
                        <option value="last_pdf">{{ __('Últim PDF') }}</option>
                        <option value="courses">{{ __('Cursos') }}</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Estadístiques --}}
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-people text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">{{ __('Total Teachers') }}</p>
                        <p class="text-lg font-bold text-blue-900">{{ $teachers->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-file-earmark-pdf text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ __('Amb PDFs') }}</p>
                        <p class="text-lg font-bold text-green-900">{{ $teachersWithPdfs->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle text-yellow-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">{{ __('Sense PDFs') }}</p>
                        <p class="text-lg font-bold text-yellow-900">{{ $teachersWithoutPdfs->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-bank text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ __('Sense IBAN') }}</p>
                        <p class="text-lg font-bold text-red-900">{{ $teachersWithoutIban->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Llista de teachers amb PDFs --}}
        <div class="space-y-4" id="teachersList">
            @foreach($teachers as $teacher)
                @php
                    $teacherPdfs = $teacher->getAllPdfs();
                @endphp
                
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow teacher-row" 
                     data-teacher-id="{{ $teacher->id }}"
                     data-name="{{ $teacher->first_name . ' ' . $teacher->last_name }}"
                     data-has-pdfs="{{ $teacher->hasPdfs() ? 'true' : 'false' }}"
                     data-iban="{{ !empty($teacher->iban) ? 'true' : 'false' }}"
                     data-pdf-count="{{ count($teacherPdfs) }}"
                     data-courses="{{ $teacher->courses->count() }}">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="bi bi-person text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    </h3>
                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                        <span><i class="bi bi-envelope mr-1"></i>{{ $teacher->user->email }}</span>
                                        <span><i class="bi bi-book mr-1"></i>{{ $teacher->courses->count() }} {{ __('cursos') }}</span>
                                        <span><i class="bi bi-bank mr-1"></i>{{ !empty($teacher->iban) ? '✅ IBAN' : '❌ IBAN' }} </span>                                          @if(!empty($teacher->payment_type))
                                                @if($teacher->payment_type == 'waived')
                                                    {{ __('campus.payment_waived') }}
                                                @elseif($teacher->payment_type == 'own')
                                                    {{ __('campus.payment_own') }}
                                                @elseif($teacher->payment_type == 'ceded')
                                                    {{ __('campus.payment_ceded') }}
                                                @endif
                                            @else
                                            {{ __('Pendent') }}
                                            @endif
                                        
                                    </div>
                                
                                </div>
                            </div>
                        </div>
                                               

                        <div class="flex items-center space-x-3">
                             
                            {{-- Accions --}}

                            <div class="flex items-center space-x-2">
                                
                                @if($teacher->hasPdfs())
                                    {{-- Veure PDFs --}}
                                    <button onclick="showTeacherPdfs({{ $teacher->id }})" 
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                        <i class="bi bi-eye mr-1"></i>
                                        {{ __('Veure') }} ({{ count($teacherPdfs) }})
                                    </button>
                                    
                                    {{-- Eliminar PDFs --}}
                                    <button onclick="confirmDeletePdfs({{ $teacher->id }})" 
                                            class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                        <i class="bi bi-trash mr-1"></i>
                                        {{ __('Eliminar') }}
                                    </button>
                                
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Detalls de PDFs (amagats per defecte) --}}
                    <div id="pdfs-{{ $teacher->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                        @if(!empty($teacherPdfs))
                            <div class="space-y-2">
                                @foreach($teacherPdfs as $pdf)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex items-center">
                                            <i class="bi bi-file-earmark-pdf text-red-500 mr-2"></i>
                                            <div>
                                                <div class="text-sm font-medium">{{ $pdf['filename'] }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $pdf['modified_date'] }} • {{ $pdf['size_formatted'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ $pdf['view_url'] }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">
                                                {{ __('Ver') }}
                                            </a>

                                            <a href="{{ $pdf['download_url'] }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">
                                                {{ __('Descarregar') }}
                                            </a>
                                            <button onclick="deletePdf({{ $teacher->id }}, '{{ $pdf['filename'] }}')" 
                                                    class="text-red-600 hover:text-red-800 text-sm underline">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                </div>
                
            @endforeach
        </div>

        @if($teachers->isEmpty())
            <div class="text-center py-12">
                <i class="bi bi-people text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ __('No s\'han trobat teachers') }}
                </h3>
                <p class="text-gray-600">
                    {{ __('No hi ha cap teacher registrat al sistema.') }}
                </p>
            </div>
        @endif
    </div>
</div>

<script>
function showTeacherPdfs(teacherId) {
    const pdfsDiv = document.getElementById('pdfs-' + teacherId);
    pdfsDiv.classList.toggle('hidden');
}

function confirmDeletePdfs(teacherId) {
    if(confirm('{{ __("Estàs segur que vols eliminar tots els PDFs d\'aquest teacher?") }}')) {
        // Aquí implementarem l'eliminació
        console.log('Eliminar PDFs del teacher:', teacherId);
    }
}

function deletePdf(teacherId, filename) {
    if(confirm('{{ __("Estàs segur que vols eliminar aquest PDF?") }}')) {
        // Crear formulari per DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/campus/teachers/${teacherId}/pdf/${filename}`;
        
        // Afegir token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Afegir method override per DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Enviar formulari
        document.body.appendChild(form);
        form.submit();
    }
}

// Filtrado i ordenació
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const nameFilter = document.getElementById('nameFilter');
    const sortBy = document.getElementById('sortBy');
    const teachersList = document.getElementById('teachersList');

    function filterAndSort() {
        const rows = teachersList.querySelectorAll('.teacher-row');
        const status = statusFilter.value;
        const name = nameFilter.value.toLowerCase();
        const sort = sortBy.value;

        let filteredRows = Array.from(rows).filter(row => {
            const hasPdfs = row.dataset.hasPdfs === 'true';
            const hasIban = row.dataset.iban === 'true';
            const teacherName = row.dataset.name.toLowerCase();

            // Filtrar per estat
            let statusMatch = true;
            switch(status) {
                case 'has_pdfs':
                    statusMatch = hasPdfs;
                    break;
                case 'no_pdfs':
                    statusMatch = !hasPdfs;
                    break;
                case 'iban_ok':
                    statusMatch = hasIban;
                    break;
                case 'iban_missing':
                    statusMatch = !hasIban;
                    break;
            }

            // Filtrar per nom
            const nameMatch = teacherName.includes(name);

            return statusMatch && nameMatch;
        });

        // Ordenar
        filteredRows.sort((a, b) => {
            switch(sort) {
                case 'name':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'pdf_count':
                    return parseInt(b.dataset.pdfCount) - parseInt(a.dataset.pdfCount);
                case 'courses':
                    return parseInt(b.dataset.courses) - parseInt(a.dataset.courses);
                default:
                    return 0;
            }
        });

        // Reordenar DOM
        filteredRows.forEach(row => teachersList.appendChild(row));
    }

    statusFilter.addEventListener('change', filterAndSort);
    nameFilter.addEventListener('input', filterAndSort);
    sortBy.addEventListener('change', filterAndSort);
});
</script>
@endsection
