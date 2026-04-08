@extends('campus.shared.layout')

@section('title', 'Pujar Nou Document')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="bi bi-cloud-upload text-blue-600 mr-3"></i>
            Pujar Nou Document
        </h1>
        <p class="text-gray-600">
            Afegeix un nou document al repositori del campus
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('campus.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Informació Bàsica -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informació Bàsica</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Títol -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Títol del Document <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title"
                                   name="title" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Referència -->
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">
                                Referència
                            </label>
                            <input type="text" 
                                   id="reference"
                                   name="reference" 
                                   placeholder="Ex: ACTA-2024-001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Descripció -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripció
                        </label>
                        <textarea id="description"
                                  name="description" 
                                  rows="3"
                                  placeholder="Breu descripció del contingut del document..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- Etiquetes -->
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                            Etiquetes
                        </label>
                        <input type="text" 
                               id="tags"
                               name="tags" 
                               placeholder="Separades per comes: acta, reunió, 2024"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Separa les etiquetes amb comes. Ajudarà a la cerca.
                        </p>
                    </div>
                </div>

                <!-- Classificació -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Classificació</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Categoria -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Categoria <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id"
                                    name="category_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecciona una categoria</option>
                                @php
                                    function buildCategoryTree($categories, $parentId = null, $level = 0) {
                                        $tree = [];
                                        foreach ($categories as $category) {
                                            if ($category->parent_id == $parentId) {
                                                $category->level = $level;
                                                $tree[] = $category;
                                                $tree = array_merge($tree, buildCategoryTree($categories, $category->id, $level + 1));
                                            }
                                        }
                                        return $tree;
                                    }
                                    
                                    $sortedCategories = buildCategoryTree($categories);
                                @endphp
                                @foreach($sortedCategories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ str_repeat('   ', $category->level) }}{{ $category->level > 0 ? '· ' : '' }}{{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Data del Document -->
                        <div>
                            <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Data del Document
                            </label>
                            <input type="date" 
                                   id="document_date"
                                   name="document_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Fitxer -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Fitxer</h2>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <i class="bi bi-cloud-upload text-gray-400 text-4xl mb-4"></i>
                        
                        <div class="mb-4">
                            <label for="file" class="cursor-pointer">
                                <span class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="bi bi-folder-open mr-2"></i>
                                    Seleccionar Fitxer
                                </span>
                                <input type="file" 
                                       id="file"
                                       name="file" 
                                       required
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                       class="hidden">
                            </label>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            Formats acceptats: PDF, Word, Excel, PowerPoint, Imatges, ZIP (màx. 10MB)
                        </p>
                        
                        <div id="fileInfo" class="hidden mt-4">
                            <div class="inline-flex items-center px-3 py-2 bg-green-100 text-green-800 rounded-md">
                                <i class="bi bi-check-circle mr-2"></i>
                                <span id="fileName"></span>
                                <span id="fileSize" class="ml-2 text-sm"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos d'Accés -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Permisos d'Accés</h2>
                    
                    <div class="space-y-4">
                        <!-- Document Públic -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_public"
                                   name="is_public" 
                                   value="1"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <label for="is_public" class="ml-2 text-sm text-gray-700">
                                Document públic (visible per tots els usuaris autenticats)
                            </label>
                        </div>

                        <!-- Access per Rol -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Access per Rol
                            </label>
                            <div class="space-y-2">
                                @foreach(['admin', 'super-admin', 'secretaria', 'gestio', 'junta', 'director', 'manager'] as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="access_roles[]" 
                                               value="{{ $role }}"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                        <label class="ml-2 text-sm text-gray-700">
                                            {{ ucfirst($role) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Si no selecciones cap rol, s'aplicarà la configuració per defecte de la categoria.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('campus.documents.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Cancel·lar
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Pujar Document
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// File selection handling
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    if (file) {
        fileName.textContent = file.name;
        fileSize.textContent = '(' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
        fileInfo.classList.remove('hidden');
    } else {
        fileInfo.classList.add('hidden');
    }
});

// Public checkbox handling
document.getElementById('is_public').addEventListener('change', function(e) {
    const roleCheckboxes = document.querySelectorAll('input[name="access_roles[]"]');
    const disabled = e.target.checked;
    
    roleCheckboxes.forEach(checkbox => {
        checkbox.disabled = disabled;
        if (disabled) {
            checkbox.checked = false;
        }
    });
});
</script>
@endsection
