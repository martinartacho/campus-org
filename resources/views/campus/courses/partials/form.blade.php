@php
    $course ??= null;
    $defaultData ??= [];
@endphp

{{-- Code --}}
<div>
    <x-input-label for="code" :value="__('campus.code')" />
    <x-text-input id="code" name="code" type="text"
        class="mt-1 block w-full"
        placeholder="Es generarà automàticament (ex: PA-002)"
        :value="old('code', $defaultData['code'] ?? $course?->code)" />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
    <small class="text-gray-500 text-xs">Deixa en blanc per generar automàticament</small>
</div>

{{-- Season --}}
<div>
    <x-input-label for="season_id" :value="__('campus.season')" />
    <select name="season_id" id="season_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            required>
        <option value="">{{ __('campus.select_season') }}</option>
        @foreach($seasons as $season)
            <option value="{{ $season->id }}"
                @selected(old('season_id', $defaultData['season_id'] ?? $course?->season_id) == $season->id)>
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
                @selected(old('category_id', $defaultData['category_id'] ?? $course?->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

{{-- Title --}}
<div>
    <x-input-label for="title" :value="__('campus.title')" />
    <x-text-input id="title" name="title" type="text"
        class="mt-1 block w-full"
        required
        :value="old('title', $defaultData['title'] ?? $course?->title)" />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

{{-- Description --}}
<div>
    <x-input-label for="description" :value="__('campus.description')" />
    <textarea id="description" name="description" rows="4"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $defaultData['description'] ?? $course?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

{{-- Dates --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="start_date" :value="__('campus.start_date')" />
        <x-text-input id="start_date" name="start_date" type="date"
            class="mt-1 block w-full"
            :value="old('start_date', $defaultData['start_date'] ?? $course?->start_date?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="end_date" :value="__('campus.end_date')" />
        <x-text-input id="end_date" name="end_date" type="date"
            class="mt-1 block w-full"
            :value="old('end_date', $defaultData['end_date'] ?? $course?->end_date?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
    </div>
</div>

{{-- Hours --}}
<div>
    <x-input-label for="hours" :value="__('campus.hours')" />
    <x-text-input id="hours" name="hours" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 25 (hores totals)"
        :value="old('hours', $defaultData['hours'] ?? $course?->hours ?? 25)" />
    <x-input-error :messages="$errors->get('hours')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1, Màxim: 1000</small>
</div>

{{-- Sessions --}}
<div>
    <x-input-label for="sessions" :value="__('campus.sessions')" />
    <x-text-input id="sessions" name="sessions" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 15 (sessions totals)"
        :value="old('sessions', $defaultData['sessions'] ?? $course?->sessions ?? 15)" />
    <x-input-error :messages="$errors->get('sessions')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1, Màxim: 100</small>
</div>

{{-- Max Students --}}
<div>
    <x-input-label for="max_students" :value="__('campus.max_students')" />
    <x-text-input id="max_students" name="max_students" type="number"
        class="mt-1 block w-full"
        placeholder="Ex: 20 (màxim alumnes)"
        :value="old('max_students', $defaultData['max_students'] ?? $course?->max_students ?? 20)" />
    <x-input-error :messages="$errors->get('max_students')" class="mt-2" />
    <small class="text-gray-500 text-xs">Mínim: 1 alumne</small>
</div>

{{-- Price & Level --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="price" :value="__('campus.price')" />
        <x-text-input id="price" name="price" type="number" step="0.01"
            class="mt-1 block w-full"
            :value="old('price', $defaultData['price'] ?? $course?->price)" />
    </div>

    <div>
        <x-input-label for="level" :value="__('campus.level')" />
        <select name="level" id="level"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">{{ __('Selecciona un nivell') }}</option>
            @foreach(\App\Models\CampusCourse::LEVELS as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('level', $defaultData['level'] ?? $course?->level) == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('level')" class="mt-2" />
    </div>
</div>

{{-- Location --}}
<div>
    <x-input-label for="location" :value="__('campus.location')" />
    <x-text-input id="location" name="location" type="text"
        class="mt-1 block w-full"
        placeholder="Ex: Aula 101, Centre Cívic"
        :value="old('location', $defaultData['location'] ?? $course?->location)" />
    <x-input-error :messages="$errors->get('location')" class="mt-2" />
</div>

{{-- Format --}}
<div>
    <x-input-label for="format" :value="__('campus.format')" />
    <select name="format" id="format"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">{{ __('Selecciona un format') }}</option>
        <option value="Presencial" 
            @selected(old('format', $defaultData['format'] ?? $course?->format) == 'Presencial')>
            Presencial
        </option>
        <option value="Online" 
            @selected(old('format', $defaultData['format'] ?? $course?->format) == 'Online')>
            Online
        </option>
        <option value="Híbrid" 
            @selected(old('format', $defaultData['format'] ?? $course?->format) == 'Híbrid')>
            Híbrid
        </option>
        <option value="Semipresencial" 
            @selected(old('format', $defaultData['format'] ?? $course?->format) == 'Semipresencial')>
            Semipresencial
        </option>
        <option value="A distància" 
            @selected(old('format', $defaultData['format'] ?? $course?->format) == 'A distància')>
            A distància
        </option>
    </select>
    <x-input-error :messages="$errors->get('format')" class="mt-2" />
</div>

{{-- Flags --}}
<div class="flex items-center gap-6">
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
            @checked(old('is_active', $defaultData['is_active'] ?? $course?->is_active ?? true))>
        <span>{{ __('campus.active') }}</span>
    </label>

    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_public" value="1"
            @checked(old('is_public', $defaultData['is_public'] ?? $course?->is_public ?? true))>
        <span>{{ __('campus.public') }}</span>
    </label>
</div>
