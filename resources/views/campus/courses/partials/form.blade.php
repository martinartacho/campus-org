@php
    $course ??= null;
@endphp

{{-- Course Type --}}
<div class="mb-6 p-4 border rounded-lg bg-gray-50">
    <h3 class="text-lg font-semibold mb-3">Tipus de Curs</h3>
    <div class="space-y-2">
        <label class="flex items-center">
            <input type="radio" name="is_base_course" value="1" 
                   @checked(old('is_base_course', $course?->is_base_course ?? true)>
            <span class="ml-2">Curs Base (Plantilla)</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="is_base_course" value="0" 
                   @checked(old('is_base_course', $course?->is_base_course ?? false)>
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
    
    function toggleFormFields() {
        const isBase = baseRadio.checked;
        
        if (isBase) {
            parentBaseSection.classList.add('hidden');
            scheduleTypeSection.classList.add('hidden');
            baseCodeSection.classList.remove('hidden');
            instanceCodeSection.classList.add('hidden');
        } else {
            parentBaseSection.classList.remove('hidden');
            scheduleTypeSection.classList.remove('hidden');
            baseCodeSection.classList.add('hidden');
            instanceCodeSection.classList.remove('hidden');
        }
    }
    
    baseRadio.addEventListener('change', toggleFormFields);
    instanceRadio.addEventListener('change', toggleFormFields);
    
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

{{-- Numbers --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <x-input-label for="credits" :value="__('campus.credits')" />
        <x-text-input id="credits" name="credits" type="number"
            class="mt-1 block w-full"
            :value="old('credits', $course?->credits)" />
    </div>

    <div>
        <x-input-label for="hours" :value="__('campus.hours')" />
        <x-text-input id="hours" name="hours" type="number"
            class="mt-1 block w-full"
            :value="old('hours', $course?->hours)" />
    </div>

    <div>
        <x-input-label for="max_students" :value="__('campus.max_students')" />
        <x-text-input id="max_students" name="max_students" type="number"
            class="mt-1 block w-full"
            :value="old('max_students', $course?->max_students)" />
    </div>
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
        <x-text-input id="level" name="level" type="text"
            class="mt-1 block w-full"
            :value="old('level', $course?->level)" />
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
