@extends('campus.shared.layout')

@section('title', 'Editar Categoria d\'Ajuda')
@section('subtitle', 'Editar categoria d\'ajuda existent')

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                Dashboard
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.help.dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                Ajuda
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.help.categories.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                Categories
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                Editar: {{ $helpCategory->name }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Categoria d\'Ajuda</h1>
        <a href="{{ route('campus.help.categories.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="bi bi-arrow-left mr-2"></i>Tornar
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <form method="POST" action="{{ route('campus.help.categories.update', $helpCategory) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nom *
                        </label>
                        <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('name', $helpCategory->name) }}" required>
                        @error('name')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Área -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Àrea *
                        </label>
                        <select name="area" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccionar àrea</option>
                            <option value="cursos" {{ old('area', $helpCategory->area) == 'cursos' ? 'selected' : '' }}>Cursos</option>
                            <option value="matricula" {{ old('area', $helpCategory->area) == 'matricula' ? 'selected' : '' }}>Matrícula</option>
                            <option value="materiales" {{ old('area', $helpCategory->area) == 'materiales' ? 'selected' : '' }}>Materiales</option>
                            <option value="configuracion" {{ old('area', $helpCategory->area) == 'configuracion' ? 'selected' : '' }}>Configuración</option>
                        </select>
                        @error('area')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Icono -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Icona
                        </label>
                        <input type="text" name="icon" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('icon', $helpCategory->icon) }}" placeholder="bi-question-circle">
                        @error('icon')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Classes d'icona Bootstrap Icons (ex: bi-question-circle)</p>
                    </div>
                    
                    <!-- Orden -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ordre
                        </label>
                        <input type="number" name="order" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('order', $helpCategory->order) }}" min="0">
                        @error('order')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descripció
                    </label>
                    <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              rows="4" placeholder="Descripció de la categoria...">{{ old('description', $helpCategory->description) }}</textarea>
                    @error('description')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               {{ old('active', $helpCategory->active) ? 'checked' : '' }}>
                        <label for="active" class="ml-2 block text-sm text-gray-900">
                            Categoria activa
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Les categories inactives no es mostraran al públic</p>
                </div>

                <!-- Información de auditoría -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Informació d'auditoria</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Creat per:</span> {{ $helpCategory->createdBy?->name ?? '-' }}
                        </div>
                        <div>
                            <span class="font-medium">Creat el:</span> {{ $helpCategory->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Actualitzat per:</span> {{ $helpCategory->updatedBy?->name ?? '-' }}
                        </div>
                        <div>
                            <span class="font-medium">Actualitzat el:</span> {{ $helpCategory->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('campus.help.categories.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="bi bi-x-circle mr-2"></i>Cancel·lar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="bi bi-check-circle mr-2"></i>Actualitzar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
