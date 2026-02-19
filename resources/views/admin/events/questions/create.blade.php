<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('site.Add Question for Event') }}: {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                <h4 class="text-lg font-medium text-blue-800 mb-2">{{ __('site.Load from Template') }}</h4>
                <div class="flex items-center space-x-2">
                    <select id="template-selector" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">{{ __('site.Select a template') }}</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" 
                                data-question="{{ $template->question }}"
                                data-type="{{ $template->type }}"
                                data-options="{{ $template->options ? json_encode($template->options) : '[]' }}"
                                data-required="{{ $template->required ? '1' : '0' }}">
                                {{ $template->template_name }} ({{ $template->type }})
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="load-template-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('site.Load Template') }}
                    </button>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.events.questions.store', $event) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="question" class="block text-sm font-medium text-gray-700">
                                {{ __('site.Question') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="question" id="question" value="{{ old('question') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('question')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                {{ __('site.Type') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>{{ __('site.Text') }}</option>
                                <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>{{ __('site.Single Choice') }}</option>
                                <option value="multiple" {{ old('type') == 'multiple' ? 'selected' : '' }}>{{ __('site.Multiple Choice') }}</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4" id="options-container" style="{{ in_array(old('type'), ['single', 'multiple']) ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('site.Options') }} <span class="text-red-500">*</span>
                            </label>
                            <p class="text-sm text-gray-500 mb-2">{{ __('site.Add at least one option for choice questions') }}</p>
                            <div id="options-list" class="space-y-2 mb-2">
                                </div>
                            <button type="button" id="add-option" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('site.Add Option') }}
                            </button>
                            @error('options')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('options.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="required" id="required" value="1" {{ old('required') ? 'checked' : '' }}
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="required" class="ml-2 block text-sm text-gray-900">{{ __('site.Required') }}</label>
                            </div>
                            @error('required')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.events.questions.index', $event) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                {{ __('site.Cancel') }}
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('site.Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM cargado - Iniciando script para gestión de preguntas');

        // --- Elementos del DOM ---
        const templateSelector = document.getElementById('template-selector');
        const loadTemplateBtn = document.getElementById('load-template-btn');
        const typeSelect = document.getElementById('type');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const addOptionButton = document.getElementById('add-option');
        const questionField = document.getElementById('question');
        const requiredField = document.getElementById('required');

        // Verificar que los elementos principales existan
        if (!typeSelect || !optionsContainer || !optionsList || !addOptionButton || !templateSelector || !loadTemplateBtn) {
            console.error('No se encontraron todos los elementos necesarios para el script.');
            return;
        }

        console.log('Elementos del DOM encontrados correctamente.');

        // --- Funciones auxiliares ---

        // Muestra/oculta el contenedor de opciones basado en el tipo de pregunta
        function toggleOptions() {
            console.log('Cambiando tipo a:', typeSelect.value);
            if (typeSelect.value === 'single' || typeSelect.value === 'multiple') {
                optionsContainer.style.display = 'block';
                console.log('Mostrando opciones.');
                // Asegura que siempre haya al menos una opción si el contenedor se muestra por primera vez
                if (optionsList.children.length === 0) {
                    addOption();
                }
            } else {
                optionsContainer.style.display = 'none';
                console.log('Ocultando opciones.');
                // Opcional: limpiar las opciones al ocultar el contenedor
                optionsList.innerHTML = '';
            }
        }

        // Añade un nuevo campo de opción al formulario
        function addOption(value = '') {
            const optionIndex = optionsList.children.length;
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-center mb-2';
            optionDiv.innerHTML = `
                <input type="text" name="options[${optionIndex}]" value="${value.replace(/"/g, '&quot;')}" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="{{ __('site.Option') }}" required>
                <button type="button" class="ml-2 text-red-600 hover:text-red-800 remove-option">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            optionsList.appendChild(optionDiv);

            // Añadir evento de clic para el botón de eliminar
            const removeButton = optionDiv.querySelector('.remove-option');
            removeButton.addEventListener('click', function() {
                optionDiv.remove();
                reindexOptions();
            });
        }

        // Reindexa los nombres de los campos de opción después de eliminar uno
        function reindexOptions() {
            const options = optionsList.querySelectorAll('input');
            options.forEach((input, index) => {
                input.name = `options[${index}]`;
            });
        }

        // --- Gestión de la carga de plantillas ---

        loadTemplateBtn.addEventListener('click', function() {
            const selectedOption = templateSelector.options[templateSelector.selectedIndex];

            if (!selectedOption.value) {
                alert('Por favor, selecciona una plantilla para cargar.');
                return;
            }
            
            // Extraer datos de los atributos `data-*` de la opción seleccionada
            const templateData = {
                question: selectedOption.dataset.question,
                type: selectedOption.dataset.type,
                options: selectedOption.dataset.options,
                required: selectedOption.dataset.required === '1'
            };

            console.log('Cargando plantilla:', templateData);

            // 1. Rellenar los campos del formulario
            questionField.value = templateData.question;
            typeSelect.value = templateData.type;
            requiredField.checked = templateData.required;

            // 2. Limpiar las opciones existentes antes de cargar las nuevas
            optionsList.innerHTML = '';
            
            // 3. Cargar las opciones si el tipo lo requiere
            if (templateData.type === 'single' || templateData.type === 'multiple') {
                try {
                    const options = JSON.parse(templateData.options);
                    options.forEach(option => {
                        addOption(option);
                    });
                } catch (e) {
                    console.error('Error al analizar las opciones de la plantilla:', e);
                }
            }

            // 4. Asegurar que la interfaz de usuario se actualice
            toggleOptions();

            alert('Plantilla cargada correctamente. Puedes editarla antes de guardar.');
        });

        // --- Inicialización de eventos y estado ---

        // Evento para el botón de añadir opción
        addOptionButton.addEventListener('click', function() {
            console.log('Botón de añadir opción clickeado.');
            addOption();
        });

        // Evento para el cambio de tipo de pregunta
        typeSelect.addEventListener('change', toggleOptions);

        // Cargar opciones si el formulario tiene errores de validación (uso de `old()`)
        @if(old('options'))
            console.log('Recuperando opciones de la sesión anterior (validación fallida).');
            @foreach(old('options') as $option)
                addOption('{{ $option }}');
            @endforeach
        @endif
        
        // Llamada inicial para establecer el estado de la UI
        toggleOptions();
        console.log('Script de preguntas inicializado correctamente.');
    });
</script>
</x-app-layout>