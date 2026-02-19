@php
    $course ??= null;
@endphp

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

{{-- Code --}}
<div>
    <x-input-label for="code" :value="__('campus.code')" />
    <x-text-input id="code" name="code" type="text"
        class="mt-1 block w-full"
        :value="old('code', $course?->code)" />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

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
