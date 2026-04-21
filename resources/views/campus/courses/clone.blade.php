@extends('campus.shared.layout')

@section('title', 'Clonar Cursos - Campus')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Clonar Cursos</h1>
        <p class="text-gray-600 mt-2">Selecciona cursos de una temporada para clonarlos en otra temporada</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form id="cloneForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Temporada Origen -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-arrow-left-right mr-1"></i>
                        Temporada Origen
                    </label>
                    <select name="source_season_id" id="sourceSeason" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecciona una temporada...</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" {{ $season->id == old('source_season_id') ? 'selected' : '' }}>
                                {{ $season->name }} ({{ \App\Models\CampusCourse::where('season_id', $season->id)->count() }} cursos)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Temporada Destino -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-flag mr-1"></i>
                        Temporada Destino
                    </label>
                    <select name="target_season_id" id="targetSeason" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecciona una temporada...</option>
                        @foreach($seasons as $season)
                            @if($currentSeason && $season->id == $currentSeason->id)
                                <option value="{{ $season->id }}" selected>
                                    {{ $season->name }} (Temporada Actual)
                                </option>
                            @else
                                <option value="{{ $season->id }}" {{ $season->id == old('target_season_id') ? 'selected' : '' }}>
                                    {{ $season->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Opciones de Clonación -->
            <div class="border-t pt-4 mb-6">
                <h3 class="text-lg font-semibold mb-4">Opciones de Clonación</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="activate_cloned" id="activateCloned" value="1"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Activar cursos clonados</span>
                            <p class="text-xs text-gray-500">Los cursos estarán disponibles inmediatamente</p>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="make_public" id="makePublic" value="1"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Hacer públicos</span>
                            <p class="text-xs text-gray-500">Los cursos serán visibles en el catálogo público</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Selección de Cursos -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-semibold mb-4">Seleccionar Cursos para Clonar</h3>
                
                <!-- Mensaje de carga -->
                <div id="loadingMessage" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="bi bi-hourglass-split text-blue-600 mr-2 animate-spin"></i>
                        <span class="text-blue-800">Cargando cursos disponibles...</span>
                    </div>
                </div>

                <!-- Mensaje de error -->
                <div id="errorMessage" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                        <span class="text-red-800" id="errorText"></span>
                    </div>
                </div>

                <!-- Lista de cursos -->
                <div id="coursesList" class="hidden">
                    <div class="mb-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Seleccionar todos</span>
                        </label>
                    </div>
                    
                    <div id="coursesContainer" class="space-y-3 max-h-96 overflow-y-auto border rounded-lg p-4">
                        <!-- Los cursos se cargarán dinámicamente -->
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-between items-center mt-6">
                    <div id="selectedCount" class="text-sm text-gray-600">
                        0 cursos seleccionados
                    </div>
                    
                    <div class="space-x-3">
                        <button type="button" onclick="resetForm()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="bi bi-arrow-clockwise mr-1"></i>
                            Reiniciar
                        </button>
                        
                        <button type="submit" id="cloneButton" disabled
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                            <i class="bi bi-files-clone mr-1"></i>
                            Clonar Cursos
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Resultados -->
    <div id="results" class="hidden mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Resultados de la Clonación</h3>
            <div id="resultsContent"></div>
        </div>
    </div>
</div>

<script>
let selectedCourses = new Set();

// Cargar cursos cuando se selecciona temporada origen
document.getElementById('sourceSeason').addEventListener('change', loadCourses);
document.getElementById('targetSeason').addEventListener('change', validateForm);

// Validar formulario cuando cambia la temporada destino
function validateForm() {
    const sourceSeason = document.getElementById('sourceSeason').value;
    const targetSeason = document.getElementById('targetSeason').value;
    
    if (sourceSeason && targetSeason && sourceSeason === targetSeason) {
        showError('La temporada origen y destino deben ser diferentes');
        document.getElementById('cloneButton').disabled = true;
    } else {
        hideError();
        document.getElementById('cloneButton').disabled = selectedCourses.size === 0;
    }
}

// Cargar cursos de la temporada origen
function loadCourses() {
    const sourceSeasonId = document.getElementById('sourceSeason').value;
    
    if (!sourceSeasonId) {
        document.getElementById('coursesList').classList.add('hidden');
        return;
    }
    
    // Mostrar mensaje de carga
    document.getElementById('loadingMessage').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('coursesList').classList.add('hidden');
    
    fetch(`/campus/courses/clone/courses?season_id=${sourceSeasonId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingMessage').classList.add('hidden');
            
            if (data.success) {
                displayCourses(data.courses);
            } else {
                showError(data.message || 'Error al cargar los cursos');
            }
        })
        .catch(error => {
            document.getElementById('loadingMessage').classList.add('hidden');
            showError('Error de conexión: ' + error.message);
        });
}

// Mostrar lista de cursos
function displayCourses(courses) {
    const container = document.getElementById('coursesContainer');
    
    if (courses.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="bi bi-inbox text-4xl mb-2"></i>
                <p>No hay cursos disponibles en esta temporada</p>
            </div>
        `;
    } else {
        container.innerHTML = courses.map(course => `
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" 
                           value="${course.id}" 
                           data-course='${JSON.stringify(course)}'
                           class="course-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900">${course.title}</h4>
                            <span class="text-sm text-gray-500">${course.code}</span>
                        </div>
                        
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Precio:</span>
                                <span class="font-medium">EUR ${course.price}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Plazas:</span>
                                <span class="font-medium">${course.max_students}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Horas:</span>
                                <span class="font-medium">${course.hours}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Nivel:</span>
                                <span class="font-medium">${course.level}</span>
                            </div>
                        </div>
                        
                        @if(course.category)
                        <div class="mt-2">
                            <span class="text-gray-500">Categoría:</span>
                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                ${course.category}
                            </span>
                        </div>
                        @endif
                        
                        @if(course.teachers)
                        <div class="mt-2">
                            <span class="text-gray-500">Profesores:</span>
                            <span class="text-sm">${course.teachers}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        `).join('');
        
        // Añadir event listeners a los checkboxes
        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCourses);
        });
    }
    
    document.getElementById('coursesList').classList.remove('hidden');
    validateForm();
}

// Actualizar cursos seleccionados
function updateSelectedCourses() {
    selectedCourses.clear();
    
    document.querySelectorAll('.course-checkbox:checked').forEach(checkbox => {
        selectedCourses.add(checkbox.value);
    });
    
    document.getElementById('selectedCount').textContent = `${selectedCourses.size} cursos seleccionados`;
    document.getElementById('cloneButton').disabled = selectedCourses.size === 0;
}

// Seleccionar todos los cursos
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.course-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCourses();
});

// Enviar formulario de clonación
document.getElementById('cloneForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (selectedCourses.size === 0) {
        showError('Debes seleccionar al menos un curso para clonar');
        return;
    }
    
    const formData = new FormData(this);
    formData.set('course_ids', Array.from(selectedCourses));
    
    // Desactivar botón durante el proceso
    const cloneButton = document.getElementById('cloneButton');
    cloneButton.disabled = true;
    cloneButton.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-1"></i> Clonando...';
    
    fetch('/campus/courses/clone', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Restaurar botón
        cloneButton.disabled = false;
        cloneButton.innerHTML = '<i class="bi bi-files-clone mr-1"></i> Clonar Cursos';
        
        displayResults(data);
    })
    .catch(error => {
        cloneButton.disabled = false;
        cloneButton.innerHTML = '<i class="bi bi-files-clone mr-1"></i> Clonar Cursos';
        showError('Error durante la clonación: ' + error.message);
    });
});

// Mostrar resultados
function displayResults(data) {
    const resultsDiv = document.getElementById('results');
    const resultsContent = document.getElementById('resultsContent');
    
    if (data.success) {
        resultsContent.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="bi bi-check-circle-fill text-green-600 mr-2"></i>
                    <span class="text-green-800 font-medium">${data.message}</span>
                </div>
            </div>
            
            ${data.cloned_courses && data.cloned_courses.length > 0 ? `
                <h4 class="font-semibold mb-3">Cursos Clonados:</h4>
                <div class="space-y-2">
                    ${data.cloned_courses.map(course => `
                        <div class="flex items-center justify-between bg-gray-50 rounded p-3">
                            <div>
                                <span class="font-medium">${course.title}</span>
                                <span class="text-gray-500 text-sm ml-2">(${course.code})</span>
                            </div>
                            <span class="text-green-600 text-sm">
                                <i class="bi bi-check-circle mr-1"></i>Clonado
                            </span>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            ${data.errors && data.errors.length > 0 ? `
                <h4 class="font-semibold mb-3 text-red-600">Errores:</h4>
                <div class="space-y-2">
                    ${data.errors.map(error => `
                        <div class="bg-red-50 border border-red-200 rounded p-3 text-red-800 text-sm">
                            ${error}
                        </div>
                    `).join('')}
                </div>
            ` : ''}
        `;
    } else {
        resultsContent.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="text-red-800 font-medium">${data.message}</span>
                </div>
            </div>
            
            ${data.errors && data.errors.length > 0 ? `
                <h4 class="font-semibold mb-3 mt-4">Errores:</h4>
                <div class="space-y-2">
                    ${data.errors.map(error => `
                        <div class="bg-red-50 border border-red-200 rounded p-3 text-red-800 text-sm">
                            ${error}
                        </div>
                    `).join('')}
                </div>
            ` : ''}
        `;
    }
    
    resultsDiv.classList.remove('hidden');
    
    // Scroll a resultados
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

// Mostrar error
function showError(message) {
    document.getElementById('errorText').textContent = message;
    document.getElementById('errorMessage').classList.remove('hidden');
    document.getElementById('loadingMessage').classList.add('hidden');
    document.getElementById('coursesList').classList.add('hidden');
}

// Ocultar error
function hideError() {
    document.getElementById('errorMessage').classList.add('hidden');
}

// Reiniciar formulario
function resetForm() {
    document.getElementById('cloneForm').reset();
    selectedCourses.clear();
    document.getElementById('selectedCount').textContent = '0 cursos seleccionados';
    document.getElementById('coursesList').classList.add('hidden');
    document.getElementById('results').classList.add('hidden');
    document.getElementById('cloneButton').disabled = true;
    hideError();
}
</script>
@endsection
