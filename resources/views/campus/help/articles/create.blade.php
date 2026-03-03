@extends('campus.shared.layout')

@section('title', 'Crear Article d\'Ajuda')
@section('subtitle', 'Crear nou article d\'ajuda')

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
            <a href="{{ route('campus.help.articles.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                Articles
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                Crear Article
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Crear Article d\'Ajuda</h1>
        <a href="{{ route('campus.help.articles.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="bi bi-arrow-left mr-2"></i>Tornar
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <form method="POST" action="{{ route('campus.help.articles.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Títol *
                        </label>
                        <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('title') }}" required>
                        @error('title')
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
                            @foreach($areas as $key => $label)
                                <option value="{{ $key }}" {{ old('area') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('area')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Context -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Context
                        </label>
                        <input type="text" name="context" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('context') }}" placeholder="Context on s'aplica aquest article">
                        @error('context')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipus
                        </label>
                        <input type="text" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('type') }}" placeholder="Tipus d'article">
                        @error('type')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Categoria
                        </label>
                        <select name="help_category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sense categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('help_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->area }})
                                </option>
                            @endforeach
                        </select>
                        @error('help_category_id')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estat *
                        </label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Orden -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ordre
                        </label>
                        <input type="number" name="order" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contenido -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Contingut *
                    </label>
                    <textarea name="content" id="content-editor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="10" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('campus.help.articles.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="bi bi-x-circle mr-2"></i>Cancel·lar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="bi bi-check-circle mr-2"></i>Crear Article
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#content-editor').summernote({
        lang: 'en-US',
        height: 300,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video', 'table', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onInit: function() {
                $('.note-editable').css('min-height', '200px');
                // Inicialitzar tooltips de Bootstrap
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        }
    });
});
</script>
@endpush
