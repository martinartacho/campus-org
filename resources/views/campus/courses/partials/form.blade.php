@php
    $course ??= null;
@endphp

{{-- Course Type --}}
<div class="mb-6 p-4 border rounded-lg bg-gray-50">
    <h3 class="text-lg font-semibold mb-3">Tipus de Curs</h3>
    <div class="space-y-2">
        <label class="flex items-center">
            <input type="radio" name="is_base_course" value="1" 
                   @checked(old('is_base_course', $course?->is_base_course ?? true))>
            <span class="ml-2">Curs Base (Plantilla)</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="is_base_course" value="0" 
                   @checked(old('is_base_course', $course?->is_base_course ?? false))>
            <span class="ml-2">Curs Impartit (Instància)</span>
        </label>
    </div>
</div>

{{-- Season --}}
<div>
    <x-input-label for="season_id" :value="__('campus.season')" />
    <select name="season_id" id="season_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">{{ __('campus.select_season') }}</option>
        @foreach($seasons as $season)
            <option value="{{ $season->id }}"
                @selected(old('season_id', $course?->season_id) == $season->id)>
                {{ $season->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('season_id')" class="mt-2" />
</div>

{{-- Category --}}
<div>
    <x-input-label for="category_id" :value="__('campus.category')" />
    <select name="category_id" id="category_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">{{ __('campus.select_category') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $course?->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

{{-- Parent Base Course (for instances) --}}
<div id="parent_base_section" class="hidden">
    <x-input-label for="parent_base_id" :value="__('Curs Base')" />
    <select name="parent_base_id" id="parent_base_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">{{ __('Selecciona un curs base') }}</option>
        @foreach(\App\Models\CampusCourse::where('is_base_course', true)->get() as $baseCourse)
            <option value="{{ $baseCourse->id }}"
                @selected(old('parent_base_id', $course?->parent_base_id) == $baseCourse->id)>
                {{ $baseCourse->base_code }} - {{ $baseCourse->title }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('parent_base_id')" class="mt-2" />
</div>

{{-- Schedule Type (for instances) --}}
<div id="schedule_type_section" class="hidden">
    <x-input-label for="schedule_type" :value="__('Horari')" />
    <select name="schedule_type" id="schedule_type"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="MAT" @selected(old('schedule_type', 'MAT'))>Matí</option>
        <option value="NIT" @selected(old('schedule_type', 'NIT'))>Nit</option>
        <option value="CAP" @selected(old('schedule_type', 'CAP'))>Cap</option>
        <option value="VES" @selected(old('schedule_type', 'VES'))>Vesprada</option>
    </select>
    <x-input-error :messages="$errors->get('schedule_type')" class="mt-2" />
</div>

{{-- Base Code (for base courses) --}}
<div id="base_code_section">
    <x-input-label for="base_code" :value="__('Codi Base')" />
    <x-text-input id="base_code" name="base_code" type="text"
        class="mt-1 block w-full"
        :value="old('base_code', $course?->base_code)"
        placeholder="Es generarà automàticament (ex: BASE-ART-001)" />
    <x-input-error :messages="$errors->get('base_code')" class="mt-2" />
</div>

{{-- Instance Code (for instances) --}}
<div id="instance_code_section" class="hidden">
    <x-input-label for="instance_code" :value="__('Codi Instància')" />
    <x-text-input id="instance_code" name="instance_code" type="text"
        class="mt-1 block w-full"
        :value="old('instance_code', $course?->instance_code)"
        placeholder="Es generarà automàticament (ex: BASE-ART-001-202526-MAT)" readonly />
    <x-input-error :messages="$errors->get('instance_code')" class="mt-2" />
</div>

{{-- Legacy Code (hidden for compatibility) --}}
<input type="hidden" name="code" value="{{ old('code', $course?->code) }}" />

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseRadio = document.querySelector('input[name="is_base_course"][value="1"]');
    const instanceRadio = document.querySelector('input[name="is_base_course"][value="0"]');
    const parentBaseSection = document.getElementById('parent_base_section');
    const scheduleTypeSection = document.getElementById('schedule_type_section');
    const baseCodeSection = document.getElementById('base_code_section');
    const instanceCodeSection = document.getElementById('instance_code_section');
    const parentBaseSelect = document.getElementById('parent_base_id');
    
    function toggleFormFields() {
        const isBase = baseRadio.checked;
        
        if (isBase) {
            parentBaseSection.classList.add('hidden');
            scheduleTypeSection.classList.add('hidden');
            baseCodeSection.classList.remove('hidden');
            instanceCodeSection.classList.add('hidden');
            // Quitar required de campos de instancia
            document.getElementById('parent_base_id').removeAttribute('required');
            document.getElementById('schedule_type').removeAttribute('required');
        } else {
            parentBaseSection.classList.remove('hidden');
            scheduleTypeSection.classList.remove('hidden');
            baseCodeSection.classList.add('hidden');
            instanceCodeSection.classList.remove('hidden');
            // Añadir required a campos de instancia
            document.getElementById('parent_base_id').setAttribute('required', '');
            document.getElementById('schedule_type').setAttribute('required', '');
        }
    }
    
    function loadBaseCourseData() {
        const baseCourseId = parentBaseSelect.value;
        console.log('Base course ID seleccionat:', baseCourseId);
        
        if (!baseCourseId) {
            console.log('No hay base course ID, limpiando formulario');
            clearFormFields();
            return;
        }
        
        console.log('Cargando datos del curso base:', baseCourseId);
        
        fetch(`/campus/courses/${baseCourseId}/data`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos del curso base:', data);
                fillFormWithBaseData(data);
            })
            .catch(error => {
                console.error('Error carregant dades del curs base:', error);
                console.error('Error completo:', error);
            });
    }
    
    function fillFormWithBaseData(baseCourse) {
        console.log('Intentando rellenar formulario con datos:', baseCourse);
        
        // Les dades estan dins de 'course' object
        const courseData = baseCourse.course || baseCourse;
        console.log('Datos del curso extraídos:', courseData);
        
        // Omplir camps amb dades del curs base
        const titleField = document.getElementById('title');
        const descriptionField = document.getElementById('description');
        const creditsField = document.getElementById('credits');
        const hoursField = document.getElementById('hours');
        const sessionsField = document.getElementById('sessions');
        const maxStudentsField = document.getElementById('max_students');
        const priceField = document.getElementById('price');
        const levelField = document.getElementById('level');
        const startDateField = document.getElementById('start_date');
        const endDateField = document.getElementById('end_date');
        const locationField = document.getElementById('location');
        const formatField = document.getElementById('format');
        
        console.log('Campos encontrados:', {
            title: !!titleField,
            description: !!descriptionField,
            credits: !!creditsField,
            hours: !!hoursField,
            sessions: !!sessionsField,
            max_students: !!maxStudentsField,
            price: !!priceField,
            level: !!levelField,
            start_date: !!startDateField,
            end_date: !!endDateField,
            location: !!locationField,
            format: !!formatField
        });
        
        // Verificar que els camps existeixin abans d'assignar valors
        if (titleField) {
            titleField.value = courseData.title ? courseData.title + ' (Instància)' : '';
            console.log('Title asignado:', titleField.value);
        }
        if (descriptionField) {
            descriptionField.value = courseData.description || '';
            console.log('Description asignada:', descriptionField.value);
        }
        if (creditsField) {
            creditsField.value = courseData.credits || '';
            console.log('Credits asignados:', creditsField.value);
        }
        if (hoursField) {
            hoursField.value = courseData.hours || '';
            console.log('Hours asignadas:', hoursField.value);
        }
        if (sessionsField) {
            sessionsField.value = courseData.sessions || '';
            console.log('Sessions asignadas:', sessionsField.value);
        }
        if (maxStudentsField) {
            maxStudentsField.value = courseData.max_students || '';
            console.log('Max students asignados:', maxStudentsField.value);
        }
        if (priceField) {
            priceField.value = courseData.price || '';
            console.log('Price asignado:', priceField.value);
        }
        if (levelField) {
            levelField.value = courseData.level || '';
            console.log('Level asignado:', levelField.value);
        }
        if (startDateField) {
            const startDate = courseData.start_date ? courseData.start_date.split('T')[0] : '';
            startDateField.value = startDate;
            console.log('Start date asignado:', startDate);
        }
        if (endDateField) {
            const endDate = courseData.end_date ? courseData.end_date.split('T')[0] : '';
            endDateField.value = endDate;
            console.log('End date asignado:', endDate);
        }
        if (locationField) {
            locationField.value = courseData.location || '';
            console.log('Location asignada:', locationField.value);
        }
        if (formatField) {
            formatField.value = courseData.format || '';
            console.log('Format asignado:', formatField.value);
        }
        
        // Mantenir checkboxes de base
        const activeCheckbox = document.querySelector('input[name="is_active"]');
        const publicCheckbox = document.querySelector('input[name="is_public"]');
        if (activeCheckbox) {
            activeCheckbox.checked = courseData.is_active || false;
            console.log('Is active asignado:', activeCheckbox.checked);
        }
        if (publicCheckbox) {
            publicCheckbox.checked = courseData.is_public || false;
            console.log('Is public asignado:', publicCheckbox.checked);
        }
        
        // Assignar season_id i category_id
        const seasonSelect = document.getElementById('season_id');
        const categorySelect = document.getElementById('category_id');
        
        if (seasonSelect && courseData.season_id) {
            seasonSelect.value = courseData.season_id;
            console.log('Season ID asignado:', courseData.season_id);
        }
        
        if (categorySelect && courseData.category_id) {
            categorySelect.value = courseData.category_id;
            console.log('Category ID asignado:', courseData.category_id);
        }
        
        console.log('Formulario rellenado completamente');
    }
    
    function clearFormFields() {
        // Netejar camps (excepte els que ja estiguin omplerts)
        const fieldsToClear = ['title', 'description', 'credits', 'hours', 'sessions', 'max_students', 'price', 'level', 'start_date', 'end_date', 'location', 'format'];
        fieldsToClear.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) field.value = '';
        });
        
        // Resetear checkboxes
        const activeCheckbox = document.querySelector('input[name="is_active"]');
        const publicCheckbox = document.querySelector('input[name="is_public"]');
        if (activeCheckbox) activeCheckbox.checked = true; // Valor per defecte
        if (publicCheckbox) publicCheckbox.checked = true; // Valor per defecte
    }
    
    // Event listeners
    baseRadio.addEventListener('change', toggleFormFields);
    instanceRadio.addEventListener('change', toggleFormFields);
    parentBaseSelect.addEventListener('change', loadBaseCourseData);
    
    // Initialize
    toggleFormFields();
});
</script>
@endpush

{{-- Title --}}
<div>
    <x-input-label for="title" :value="__('campus.title')" />
    <x-text-input id="title" name="title" type="text"
        class="mt-1 block w-full"
        required
        :value="old('title', $course?->title)" />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

{{-- Description --}}
<div>
    <x-input-label for="description" :value="__('campus.description')" />
    <textarea id="description" name="description" rows="4"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $course?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

{{-- Dates --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="start_date" :value="__('campus.start_date')" />
        <x-text-input id="start_date" name="start_date" type="date"
            class="mt-1 block w-full"
            :value="old('start_date', $course?->start_date?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="end_date" :value="__('campus.end_date')" />
        <x-text-input id="end_date" name="end_date" type="date"
            class="mt-1 block w-full"
            :value="old('end_date', $course?->end_date?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
    </div>
</div>

{{-- Credits --}}
<div>
    <x-input-label for="credits" :value="__('campus.credits')" />
    <x-text-input id="credits" name="credits" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 1 (crèdits ECTS)"
        :value="old('credits', $course?->credits ?? 1)" />
    <x-input-error :messages="$errors->get('credits')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1, Màxim: 240</small>
</div>

{{-- Hours --}}
<div>
    <x-input-label for="hours" :value="__('campus.hours')" />
    <x-text-input id="hours" name="hours" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 25 (hores totals)"
        :value="old('hours', $course?->hours ?? 25)" />
    <x-input-error :messages="$errors->get('hours')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1, Màxim: 1000</small>
</div>

{{-- Sessions --}}
<div>
    <x-input-label for="sessions" :value="__('campus.sessions')" />
    <x-text-input id="sessions" name="sessions" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 15 (sessions totals)"
        :value="old('sessions', $course?->sessions ?? 15)" />
    <x-input-error :messages="$errors->get('sessions')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1, Màxim: 100</small>
</div>

{{-- Max Students --}}
<div>
    <x-input-label for="max_students" :value="__('campus.max_students')" />
    <x-text-input id="max_students" name="max_students" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 20 (màxim alumnes)"
        :value="old('max_students', $course?->max_students ?? 20)" />
    <x-input-error :messages="$errors->get('max_students')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1 alumne</small>
</div>

{{-- Price & Level --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="price" :value="__('campus.price')" />
        <x-text-input id="price" name="price" type="number" step="0.01"
            class="mt-1 block w-full"
            :value="old('price', $course?->price)" />
    </div>

    <div>
        <x-input-label for="level" :value="__('campus.level')" />
        <select name="level" id="level"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">{{ __('Selecciona un nivell') }}</option>
            @foreach(\App\Models\CampusCourse::LEVELS as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('level', $course?->level) == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('level')" class="mt-2" />
    </div>
</div>

{{-- Flags --}}
<div class="flex items-center gap-6">
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
            @checked(old('is_active', $course?->is_active))>
        <span>{{ __('campus.active') }}</span>
    </label>

    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_public" value="1"
            @checked(old('is_public', $course?->is_public))>
        <span>{{ __('campus.public') }}</span>
    </label>
</div>
