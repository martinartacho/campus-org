@extends('campus.shared.layout')

@section('title', __('campus.import_courses'))
@section('subtitle', __('campus.import_courses_description'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.courses.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.courses') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.import_courses') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Información de importación -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="bi bi-info-circle mr-2"></i>{{ __('campus.import_information') }}
        </h3>
        
        <div class="space-y-3 text-sm text-blue-800">
            <p>{{ __('campus.import_information_alert') }}</p>
            
            <div class="bg-white rounded p-4 border border-blue-200">
                <h4 class="font-semibold mb-2">{{ __('campus.import_format_expected') }}</h4>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>{{ __('campus.import_format_columns') }}</li>
                    <li>{{ __('campus.import_format_files') }}</li>
                    <li>{{ __('campus.import_format_headers') }}</li>
                </ul>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('campus.courses.import.template') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                    <i class="bi bi-download mr-2"></i>
                    {{ __('campus.download_template') }}
                </a>
                
                <button onclick="showFormatHelp()" 
                        class="text-blue-600 hover:text-blue-800 text-sm underline">
                    {{ __('campus.import_show_format') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario de importación -->
    <div class="bg-white shadow rounded-lg">
        <form id="importForm" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Selección de temporada -->
                <div>
                    <label for="season_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('campus.select_season') }}
                    </label>
                    <select name="season_id" id="season_id" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('campus.use_current_season') }}</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" 
                                    {{ $season->is_current ? 'selected' : '' }}>
                                {{ $season->name }} {{ $season->is_current ? '(' . __('campus.season_current') . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Archivo CSV -->
                <div>
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('campus.import_file_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="csv_file" 
                           id="csv_file" 
                           accept=".csv,.txt"
                           required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('campus.import_file_info') }}
                    </p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('campus.courses.index') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    <i class="bi bi-arrow-left mr-2"></i>{{ __('campus.back') }}
                </a>
                
                <div class="space-x-3">
                    <button type="button" 
                            onclick="validateFile()"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        <i class="bi bi-check-circle mr-2"></i>{{ __('campus.import_validate') }}
                    </button>
                    
                    <button type="submit" 
                            id="submitBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="bi bi-upload mr-2"></i>{{ __('campus.import_submit') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Resultados de importación -->
    <div id="importResults" class="hidden mt-6">
        <!-- Se llenará dinámicamente -->
    </div>

    <!-- Modal de formato -->
    <div id="formatModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('campus.import_format_detailed') }}
                </h3>
                
                <div class="bg-gray-50 rounded p-4 text-xs overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border p-2 text-left">Columna</th>
                                <th class="border p-2 text-left">Descripción</th>
                                <th class="border p-2 text-left">Obligatorio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border p-2 font-mono">Nom</td>
                                <td class="border p-2">Nombre del profesor</td>
                                <td class="border p-2 text-center">✓</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="border p-2 font-mono">COGNOM 1</td>
                                <td class="border p-2">Primer apellido</td>
                                <td class="border p-2 text-center">✓</td>
                            </tr>
                            <tr>
                                <td class="border p-2 font-mono">COGNOM 2</td>
                                <td class="border p-2">Segundo apellido</td>
                                <td class="border p-2 text-center">-</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="border p-2 font-mono">CORREU</td>
                                <td class="border p-2">Email del profesor</td>
                                <td class="border p-2 text-center">✓</td>
                            </tr>
                            <tr>
                                <td class="border p-2 font-mono">TÍTOL CURS</td>
                                <td class="border p-2">Título del curso</td>
                                <td class="border p-2 text-center">✓</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="border p-2 font-mono">nombre sessions</td>
                                <td class="border p-2">Número de sesiones</td>
                                <td class="border p-2 text-center">-</td>
                            </tr>
                            <tr>
                                <td class="border p-2 font-mono">Preu/sessió</td>
                                <td class="border p-2">Precio por sesión</td>
                                <td class="border p-2 text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button onclick="closeFormatHelp()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ __('campus.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let importResults = null;

// Validar archivo antes de importar
function validateFile() {
    const fileInput = document.getElementById('csv_file');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Has de seleccionar un fitxer');
        return;
    }
    
    if (!file.name.match(/\.(csv|txt)$/i)) {
        alert('El fitxer ha de ser de tipus CSV o TXT');
        return;
    }
    
    alert('El fitxer sembla correcte. Pots procedir amb la importació.');
}

// Mostrar ayuda del formato
function showFormatHelp() {
    document.getElementById('formatModal').classList.remove('hidden');
}

function closeFormatHelp() {
    document.getElementById('formatModal').classList.add('hidden');
}

// Manejar envío del formulario
document.getElementById('importForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split mr-2"></i>{{ __("campus.import_processing") }}';
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("campus.courses.import.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        // Mostrar resultados
        showImportResults(result);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error de connexió. Torna-ho a provar.');
        
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Mostrar resultados de importación
function showImportResults(result) {
    const resultsDiv = document.getElementById('importResults');
    resultsDiv.classList.remove('hidden');
    
    if (result.success) {
        resultsDiv.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-4">
                    <i class="bi bi-check-circle mr-2"></i>{{ __('campus.import_success') }}
                </h3>
                
                <div class="text-green-800">
                    <p class="mb-3">${result.message}</p>
                    
                    <div class="bg-white rounded p-4 border border-green-200">
                        <h4 class="font-semibold mb-2">{{ __('campus.import_summary') }}</h4>
                        <ul class="space-y-1 text-sm">
                            <li>👥 {{ __('Professors creats') }}: <strong>${result.resum.teachers_creats}</strong></li>
                            <li>📚 {{ __('Cursos creats') }}: <strong>${result.resum.courses_creats}</strong></li>
                            <li>❌ {{ __('Errors') }}: <strong>${result.resum.errors}</strong></li>
                        </ul>
                    </div>
                    
                    ${result.incidencies && result.incidencies.length > 0 ? `
                        <div class="mt-4 bg-yellow-50 rounded p-4 border border-yellow-200">
                            <h4 class="font-semibold mb-2 text-yellow-800">{{ __('campus.import_warnings') }}</h4>
                            <ul class="space-y-1 text-sm text-yellow-800">
                                ${result.incidencies.map(inc => `<li>• ${inc}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    
                    <div class="mt-4 flex space-x-3">
                        <a href="{{ route('campus.courses.index') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <i class="bi bi-list mr-2"></i>{{ __('campus.view_courses') }}
                        </a>
                        <button onclick="location.reload()" 
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            <i class="bi bi-arrow-clockwise mr-2"></i>{{ __('campus.import_another') }}
                        </button>
                    </div>
                </div>
            </div>
        `;
    } else {
        resultsDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-900 mb-4">
                    <i class="bi bi-exclamation-triangle mr-2"></i>{{ __('campus.import_error') }}
                </h3>
                
                <div class="text-red-800">
                    <p class="mb-3">${result.message}</p>
                    
                    ${result.incidencies && result.incidencies.length > 0 ? `
                        <div class="bg-white rounded p-4 border border-red-200">
                            <h4 class="font-semibold mb-2">{{ __('campus.import_errors_detail') }}</h4>
                            <ul class="space-y-1 text-sm">
                                ${result.incidencies.map(inc => `<li>• ${inc}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    
                    <div class="mt-4">
                        <button onclick="location.reload()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            <i class="bi bi-arrow-clockwise mr-2"></i>{{ __('campus.try_again') }}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Scroll a resultados
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection
