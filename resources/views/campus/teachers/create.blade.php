@extends('campus.shared.layout')

@section('title', __('Nou Professor'))
@section('subtitle', __('Crear nou professor'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.dashboard')
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('Professors') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('Nou Professor') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Nou Professor</h1>
        <a href="{{ route('campus.teachers.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Tornar
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <form action="{{ route('campus.teachers.store') }}" method="POST">
                @csrf
                
                <!-- Datos Personales -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">Dades Personals</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Cognoms <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telèfon
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">
                                DNI
                            </label>
                            <input type="text" 
                                   id="dni" 
                                   name="dni" 
                                   value="{{ old('dni') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('dni')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="iban" class="block text-sm font-medium text-gray-700 mb-2">
                                IBAN
                            </label>
                            <input type="text" 
                                   id="iban" 
                                   name="iban" 
                                   value="{{ old('iban') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('iban')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_titular" class="block text-sm font-medium text-gray-700 mb-2">
                                Titular del Compte
                            </label>
                            <input type="text" 
                                   id="bank_titular" 
                                   name="bank_titular" 
                                   value="{{ old('bank_titular') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('bank_titular')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fiscal_situation" class="block text-sm font-medium text-gray-700 mb-2">
                                Situació Fiscal
                                <span class="text-xs text-gray-500 ml-2">(Valores vàlids: autonomo, empleat, pensionista, altres)</span>
                            </label>
                            <input type="text" 
                                   id="fiscal_situation" 
                                   name="fiscal_situation" 
                                   value="{{ old('fiscal_situation') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Introdueix la situació fiscal...">
                            @error('fiscal_situation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="degree" class="block text-sm font-medium text-gray-700 mb-2">
                                Títol Acadèmic
                            </label>
                            <input type="text" 
                                   id="degree" 
                                   name="degree" 
                                   value="{{ old('degree') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('degree')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                                Especialització
                            </label>
                            <input type="text" 
                                   id="specialization" 
                                   name="specialization" 
                                   value="{{ old('specialization') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('specialization')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Títol Professional
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="Dr., Prof., etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hiring_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Data de Contractació
                            </label>
                            <input type="date" 
                                   id="hiring_date" 
                                   name="hiring_date" 
                                   value="{{ old('hiring_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('hiring_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Estat
                            </label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Actiu</option>
                                <option value="inactive">Inactiu</option>
                                <option value="on_leave">De Baixa</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Adreça
                            </label>
                            <input type="text" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Codi Postal
                            </label>
                            <input type="text" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Ciutat
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="areas" class="block text-sm font-medium text-gray-700 mb-2">
                                Àrees d'Especialització
                            </label>
                            <textarea 
                                   id="areas" 
                                   name="areas" 
                                   rows="3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Introduïu les àrees d'especialització separades per comes...">{{ old('areas') }}</textarea>
                            @error('areas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Asignación de Cursos -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">Assignació de Cursos</h2>
                    <div id="courses-container" class="space-y-4">
                        <div class="course-item bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Curs
                                    </label>
                                    <div class="flex space-x-2">
                                        <select name="courses[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleNewCourseForm(this)">
                                            <option value="">Seleccionar curs...</option>
                                            <option value="new">+ Crear nou curs...</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Hores Assignades
                                    </label>
                                    <input type="number" 
                                           name="hours_assigned[]" 
                                           min="0" 
                                           step="1"
                                           placeholder="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Rol
                                    </label>
                                    <select name="role[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="teacher">Professor</option>
                                        <option value="assistant">Assistent</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Camp per a "Crear nou curs" (es mostra només si es selecciona) -->
                            <div class="new-course-name" style="display: none;" class="mt-4 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mb-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                                        <div>
                                            <h5 class="text-sm font-semibold text-yellow-800 mb-1">Requisits per crear un nou curs</h5>
                                            <ul class="text-xs text-yellow-700 space-y-1">
                                                <li>• <strong>Codi del curs</strong>: ha de ser únic i no pot existir</li>
                                                <li>• <strong>Nom del curs</strong>: obligatori</li>
                                                <li>• <strong>Hores assignades</strong>: seran les hores totals del curs</li>
                                                <li>• Si el codi ja existeix, no es podrà crear el professor</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="text-sm font-semibold mb-3 text-blue-900">Crear Nou Curs</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Nom del Curs <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="new_course_title[]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Introduïu el nom del nou curs..."
                                               value="{{ old('new_course_title.0') }}"
                                               required>
                                        @error('new_course_title.0')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Codi del Curs <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="new_course_code[]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Ex: CURS001"
                                               value="{{ old('new_course_code.0') }}"
                                               onblur="this.value = this.value.trim().toUpperCase()"
                                               required>
                                        @error('new_course_code.0')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Les hores assignades seran les hores totals del curs. La resta de dades s'assignaran automàticament.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" 
                            onclick="addCourseField()" 
                            class="mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Afegir Curs
                    </button>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('campus.teachers.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Cancel·lar
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-save mr-2"></i>Crear Professor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, formulario de profesor listo');
});

function addCourseField() {
    const container = document.getElementById('courses-container');
    const courseItem = document.createElement('div');
    courseItem.className = 'course-item bg-gray-50 p-4 rounded-lg';
    
    courseItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Curs
                </label>
                <div class="flex space-x-2">
                    <select name="courses[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleNewCourseForm(this)">
                        <option value="">Seleccionar curs...</option>
                        <option value="new">+ Crear nou curs...</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Hores Assignades
                </label>
                <input type="number" 
                       name="hours_assigned[]" 
                       min="0" 
                       step="1"
                       placeholder="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Rol
                </label>
                <div class="flex space-x-2">
                    <select name="role[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="teacher">Professor</option>
                        <option value="assistant">Assistent</option>
                    </select>
                    <button type="button" 
                            onclick="this.closest('.course-item').remove()" 
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- Camp per a "Crear nou curs" (es mostra només si es selecciona) -->
            <div class="new-course-name" style="display: none;" class="mt-4 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                        <div>
                            <h5 class="text-sm font-semibold text-yellow-800 mb-1">Requisits per crear un nou curs</h5>
                            <ul class="text-xs text-yellow-700 space-y-1">
                                <li>• <strong>Codi del curs</strong>: ha de ser únic i no pot existir</li>
                                <li>• <strong>Nom del curs</strong>: obligatori</li>
                                <li>• <strong>Hores assignades</strong>: seran les hores totals del curs</li>
                                <li>• Si el codi ja existeix, no es podrà crear el professor</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <h4 class="text-sm font-semibold mb-3 text-blue-900">Crear Nou Curs</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nom del Curs <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="new_course_title[]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Introduïu el nom del nou curs..."
                               required>
                    @error('new_course_title.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Codi del Curs <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="new_course_code[]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ex: CURS001"
                               onblur="this.value = this.value.trim().toUpperCase()"
                               required>
                    @error('new_course_code.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Les hores assignades seran les hores totals del curs. La resta de dades s'assignaran automàticament.
                </p>
            </div>
        </div>
    `;
    
    container.appendChild(courseItem);
}

function toggleNewCourseForm(select) {
    const courseItem = select.closest('.course-item');
    const newCourseName = courseItem.querySelector('.new-course-name');
    
    // Verificar si se encontró el campo
    if (!newCourseName) {
        console.error('No se encontró el campo de nombre de nuevo curso');
        console.log('courseItem:', courseItem);
        console.log('innerHTML:', courseItem ? courseItem.innerHTML : 'null');
        return;
    }
    
    if (select.value === 'new') {
        newCourseName.style.display = 'block';
    } else {
        newCourseName.style.display = 'none';
        // Limpiar campos del formulario
        const inputs = newCourseName.querySelectorAll('input');
        if (inputs) {
            inputs.forEach(field => {
                field.value = '';
            });
        }
    }
}
</script>
@endsection
