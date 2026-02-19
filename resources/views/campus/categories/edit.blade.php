{{-- resources/views/campus/categories/edit.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.edit_category'))
@section('subtitle', __('campus.edit_category') . ': ' . $category->name)

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.categories.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.categories') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.edit') }}</span>
        </div>
    </li>
@endsection

@section('content')
    <form action="{{ route('campus.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            {{-- Información básica --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('campus.category_basic_info') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nombre --}}
                    <div>
                        <x-input-label for="name" :value="__('campus.category_name') . ' *'" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                     value="{{ old('name', $category->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    
                    {{-- Color --}}
                    <div>
                        <x-input-label for="color" :value="__('campus.category_color')" />
                        <x-campus-color-select id="color" name="color" class="mt-1 block w-full" 
                                            :selected="old('color', $category->color)" />
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>
                    
                    {{-- Icono --}}
                    <div>
                        <x-input-label for="icon" :value="__('campus.category_icon')" />
                        <x-campus-icon-select id="icon" name="icon" class="mt-1 block w-full" 
                                            :selected="old('icon', $category->icon)" />
                        <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                    </div>
                    
                    {{-- Categoría padre --}}
                    <div>
                        <x-input-label for="parent_id" :value="__('campus.category_parent')" />
                        <x-campus-parent-category-select id="parent_id" name="parent_id" 
                                                       :categories="$parentCategories" 
                                                       :selected="old('parent_id', $category->parent_id)"
                                                       :exclude="$category->id"
                                                       class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                    </div>
                    
                    {{-- Orden --}}
                    <div>
                        <x-input-label for="order" :value="__('campus.category_order')" />
                        <x-text-input id="order" name="order" type="number" class="mt-1 block w-full" 
                                     value="{{ old('order', $category->order) }}" />
                        <x-input-error :messages="$errors->get('order')" class="mt-2" />
                    </div>
                    
                    {{-- Descripción --}}
                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="__('campus.category_description')" />
                        <textarea id="description" name="description" rows="3" 
                                 class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </div>
            
            {{-- Estado --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-toggle-on me-2"></i>
                    {{ __('campus.category_status') }}
                </h3>
                
                <div class="space-y-4">
                    {{-- Activa --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_active" name="is_active" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">{{ __('campus.category_is_active') }}</label>
                            <p class="text-gray-500">{{ __('campus.category_active_help') }}</p>
                        </div>
                    </div>
                    
                    {{-- Destacada --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_featured" name="is_featured" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_featured" class="font-medium text-gray-700">{{ __('campus.category_is_featured') }}</label>
                            <p class="text-gray-500">{{ __('campus.category_featured_help') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Vista previa --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-eye me-2"></i>
                    {{ __('campus.preview') }}
                </h3>
                
                <div id="category-preview" class="p-4 rounded-lg border border-gray-300">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 {{ $category->color ? 'bg-' . $category->color . '-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center">
                            <i class="bi bi-{{ $category->icon ?? 'tag' }} {{ $category->color ? 'text-' . $category->color . '-600' : 'text-blue-600' }} text-xl" id="preview-icon"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900" id="preview-name">{{ $category->name }}</div>
                            <div class="text-sm text-gray-500" id="preview-description">{{ $category->description }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Botones --}}
            <div class="flex justify-end space-x-4">
                <x-campus-secondary-button href="{{ route('campus.categories.index') }}">
                    <i class="bi bi-x-lg me-2"></i>
                    {{ __('campus.cancel') }}
                </x-campus-secondary-button>
                
                <x-campus-primary-button type="submit">
                    <i class="bi bi-check-lg me-2"></i>
                    {{ __('campus.update_category') }}
                </x-campus-primary-button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar vista previa
        function updatePreview() {
            const name = document.getElementById('name').value || 'Nom de la categoria';
            const description = document.getElementById('description').value || '';
            const color = document.getElementById('color').value || 'blue';
            const icon = document.getElementById('icon').value || 'tag';
            
            // Actualizar contenido
            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-description').textContent = description;
            
            // Actualizar color
            const previewDiv = document.getElementById('category-preview').querySelector('.h-12.w-12');
            previewDiv.className = `h-12 w-12 bg-${color}-100 rounded-lg flex items-center justify-center`;
            
            // Actualizar icono
            const iconElement = document.getElementById('preview-icon');
            iconElement.className = `bi bi-${icon} text-${color}-600 text-xl`;
        }
        
        // Escuchar cambios en los campos
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);
        document.getElementById('color').addEventListener('change', updatePreview);
        document.getElementById('icon').addEventListener('change', updatePreview);
        
        // Inicializar vista previa
        updatePreview();
    });
</script>
@endpush