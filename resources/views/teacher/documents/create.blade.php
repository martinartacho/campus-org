@extends('layouts.app')

@section('title', 'Crear Nou Document')

@section('breadcrumbs')
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <a href="{{ route('teacher.documents.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-gray-700">
        Meus Documents
    </a>
</li>
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-1 text-sm font-medium text-gray-500">Crear Document</span>
</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="bi bi-plus-circle text-green-600 mr-3"></i>
                Crear Nou Document
            </h1>
            <p class="text-gray-600">
                Carrega un nou document per als teus estudiants
            </p>
        </div>

        <!-- Formulario -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('teacher.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                
                <!-- Información Básica -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informació Bàsica</h3>
                    
                    <!-- Título -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Títol <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               required
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Títol del document">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripció
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Breu descripció del document">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Etiquetas -->
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                            Etiquetes
                        </label>
                        <input type="text" 
                               id="tags" 
                               name="tags" 
                               value="{{ old('tags') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Separades per comes: matemàtiques, exercicis, examen">
                        @error('tags')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contexto Educativo -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Context Educatiu</h3>
                    
                    <!-- Categoría -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoria <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" 
                                name="category_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Selecciona una categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Curso -->
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Curs (opcional)
                        </label>
                        <select id="course_id" 
                                name="course_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Sense assignar a cap curs</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Documento -->
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipus de Document <span class="text-red-500">*</span>
                        </label>
                        <select id="document_type" 
                                name="document_type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Selecciona el tipus</option>
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('document_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Visibilidad para Estudiantes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Visibilitat per a Estudiants <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            @foreach($visibilityOptions as $value => $label)
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="student_visibility" 
                                           value="{{ $value }}" 
                                           {{ old('student_visibility') == $value ? 'checked' : ($value == 'course' ? 'checked' : '') }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('student_visibility')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Año Académico -->
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Any Acadèmic
                        </label>
                        <input type="number" 
                               id="academic_year" 
                               name="academic_year" 
                               min="2000" 
                               max="2100"
                               value="{{ old('academic_year') ?? (date('Y') >= 8 ? date('Y') + 1 : date('Y')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Ex: 2026">
                        @error('academic_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha del Documento -->
                    <div>
                        <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Data del Document
                        </label>
                        <input type="date" 
                               id="document_date" 
                               name="document_date" 
                               value="{{ old('document_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('document_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Archivo -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Arxiu</h3>
                    
                    <!-- File Upload -->
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Arxiu <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               id="file" 
                               name="file" 
                               required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Màxim 10MB. Formats acceptats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR
                        </p>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('teacher.documents.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="bi bi-x-circle mr-2"></i>
                        Cancel·lar
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="bi bi-upload mr-2"></i>
                        Pujar Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
