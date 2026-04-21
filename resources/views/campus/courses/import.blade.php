@extends('campus.shared.layout')

@section('title', 'Importar Cursos - Campus')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Importar Cursos</h1>
        <p class="text-gray-600 mt-2">Importa cursos desde un archivo CSV</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form id="importForm" action="{{ route('importar.cursos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-file-earmark-arrow-up mr-1"></i>
                    Archivo CSV
                </label>
                <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Formato: CSV o TXT (máx 10MB)</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-calendar3 mr-1"></i>
                    Temporada Destino
                </label>
                <select name="season_id" id="seasonId" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecciona una temporada...</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ $season->is_current ? 'selected' : '' }}>
                            {{ $season->name }} {{ $season->is_current ? '(Actual)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="confirm_responsibility" id="confirmResponsibility" value="1"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">
                        Confirmo que soy responsable de los datos importados
                    </span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="button" onclick="validateFile()" 
                        class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">
                    <i class="bi bi-check-circle mr-1"></i>
                    Validar Archivo
                </button>
                
                <button type="submit" id="importButton"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="bi bi-upload mr-1"></i>
                    Importar Cursos
                </button>
                
                <a href="{{ route('importar.cursos.template') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="bi bi-download mr-1"></i>
                    Descargar Plantilla
                </a>
            </div>
        </form>
    </div>

    <!-- Validación -->
    <div id="validationResult" class="hidden mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Resultado de Validación</h3>
            <div id="validationContent"></div>
        </div>
    </div>

    <!-- Resultados -->
    <div id="importResult" class="hidden mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Resultados de Importación</h3>
            <div id="importContent"></div>
        </div>
    </div>
</div>

<script>
function validateFile() {
    const fileInput = document.getElementById('csvFile');
    const seasonId = document.getElementById('seasonId');
    const validationResult = document.getElementById('validationResult');
    const validationContent = document.getElementById('validationContent');
    
    if (!fileInput.files[0]) {
        alert('Por favor selecciona un archivo CSV');
        return;
    }
    
    if (!seasonId.value) {
        alert('Por favor selecciona una temporada');
        return;
    }
    
    const formData = new FormData();
    formData.append('csv_file', fileInput.files[0]);
    
    // Mostrar loading
    validationContent.innerHTML = `
        <div class="flex items-center">
            <i class="bi bi-hourglass-split text-blue-600 mr-2 animate-spin"></i>
            <span class="text-blue-800">Validando archivo...</span>
        </div>
    `;
    validationResult.classList.remove('hidden');
    
    fetch('{{ route('importar.cursos.validate') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const hasIssues = data.has_issues;
            const warnings = data.warnings || [];
            
            validationContent.innerHTML = `
                <div class="mb-4">
                    <div class="flex items-center ${hasIssues ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200'} rounded-lg p-4">
                        <i class="bi ${hasIssues ? 'bi-exclamation-triangle text-yellow-600' : 'bi-check-circle text-green-600'} mr-2"></i>
                        <span class="${hasIssues ? 'text-yellow-800' : 'text-green-800'} font-medium">
                            ${data.message}
                        </span>
                    </div>
                </div>
                
                ${warnings.length > 0 ? `
                    <h4 class="font-semibold mb-3 text-yellow-600">Advertencias:</h4>
                    <div class="space-y-2">
                        ${warnings.map(warning => `
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-yellow-800 text-sm">
                                <div class="flex items-center">
                                    <i class="bi bi-info-circle mr-2"></i>
                                    <span>Fila ${warning.row}: ${warning.message}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
                
                <div class="mt-4">
                    <button onclick="proceedWithImport()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-upload mr-1"></i>
                        Continuar con Importación
                    </button>
                </div>
            `;
        } else {
            validationContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                        <span class="text-red-800 font-medium">${data.message}</span>
                    </div>
                    ${data.errors ? `
                        <div class="mt-2">
                            <ul class="text-red-700 text-sm">
                                ${Object.values(data.errors).map(error => `<li>${error}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            `;
        }
    })
    .catch(error => {
        validationContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="text-red-800">Error de conexión: ${error.message}</span>
                </div>
            </div>
        `;
    });
}

function proceedWithImport() {
    document.getElementById('importForm').submit();
}

// Validación del formulario
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('csvFile');
    const importButton = document.getElementById('importButton');
    const importResult = document.getElementById('importResult');
    const importContent = document.getElementById('importContent');
    
    if (!fileInput.files[0]) {
        alert('Por favor selecciona un archivo CSV');
        return;
    }
    
    // Mostrar loading
    importButton.disabled = true;
    importButton.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-1"></i> Importando...';
    
    const formData = new FormData(this);
    
    fetch('{{ route('importar.cursos.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        importButton.disabled = false;
        importButton.innerHTML = '<i class="bi bi-upload mr-1"></i> Importar Cursos';
        
        importResult.classList.remove('hidden');
        
        if (data.success) {
            importContent.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="bi bi-check-circle text-green-600 mr-2"></i>
                        <span class="text-green-800 font-medium">${data.message}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">${data.resum.teachers_creats}</div>
                        <div class="text-sm text-blue-800">Profesores Creados</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">${data.resum.courses_creats}</div>
                        <div class="text-sm text-green-800">Cursos Creados</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded p-4 text-center">
                        <div class="text-2xl font-bold text-gray-600">${data.resum.errors}</div>
                        <div class="text-sm text-gray-800">Errores</div>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <button onclick="location.reload()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-plus-circle mr-1"></i>
                        Importar Más Cursos
                    </button>
                    
                    <a href="/manager/courses" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <i class="bi bi-list mr-1"></i>
                        Ver Cursos
                    </a>
                </div>
            `;
            
            // Ocultar formulario
            document.getElementById('importForm').classList.add('hidden');
            document.getElementById('validationResult').classList.add('hidden');
            
        } else {
            importContent.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                        <span class="text-red-800 font-medium">${data.message}</span>
                    </div>
                </div>
                
                ${data.incidencies && data.incidencies.length > 0 ? `
                    <h4 class="font-semibold mb-3 text-red-600">Incidencias:</h4>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        ${data.incidencies.map(incidence => `
                            <div class="bg-red-50 border border-red-200 rounded p-3 text-red-800 text-sm">
                                ${incidence}
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
                
                <div class="mt-4">
                    <button onclick="location.reload()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-arrow-clockwise mr-1"></i>
                        Reintentar
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        importButton.disabled = false;
        importButton.innerHTML = '<i class="bi bi-upload mr-1"></i> Importar Cursos';
        
        importResult.classList.remove('hidden');
        importContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="text-red-800">Error durante la importación: ${error.message}</span>
                </div>
            </div>
        `;
    });
});
</script>
@endsection
