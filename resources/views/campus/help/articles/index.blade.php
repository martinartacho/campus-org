@extends('campus.shared.layout')

@section('title', 'Articles d\'Ajuda')
@section('subtitle', 'Gestió d\'articles d\'ajuda del campus')

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
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                Articles
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Articles d'Ajuda</h1>
        <a href="{{ route('campus.help.articles.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="bi bi-plus-circle mr-2"></i>Nou Article
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">
                    <i class="bi bi-funnel mr-2"></i>Filtres
                </h3>
                
                <form method="GET" action="{{ route('campus.help.articles.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Filtro por búsqueda -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Cerca
                            </label>
                            <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ request('search') }}" placeholder="Cercar articles...">
                        </div>
                        
                        <!-- Filtro por área -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Àrea
                            </label>
                            <select name="area" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Totes</option>
                                <option value="cursos" {{ request('area') == 'cursos' ? 'selected' : '' }}>Cursos</option>
                                <option value="matricula" {{ request('area') == 'matricula' ? 'selected' : '' }}>Matrícula</option>
                                <option value="materiales" {{ request('area') == 'materiales' ? 'selected' : '' }}>Materiales</option>
                                <option value="configuracion" {{ request('area') == 'configuracion' ? 'selected' : '' }}>Configuración</option>
                            </select>
                        </div>
                        
                        <!-- Filtro por estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Estat
                            </label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tots</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validat</option>
                                <option value="obsolete" {{ request('status') == 'obsolete' ? 'selected' : '' }}>Obsolet</option>
                            </select>
                        </div>
                        
                        <!-- Botones de filtro -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="bi bi-search mr-2"></i>Filtrar
                            </button>
                            <a href="{{ route('campus.help.articles.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="bi bi-arrow-clockwise mr-2"></i>Netejar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de artículos -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            @if($articles->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Títol
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Àrea
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Creat per
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actualitzat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Accions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($articles as $article)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $article->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $article->slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($article->area) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($article->status)
                                        @case('draft')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Borrador</span>
                                            @break
                                        @case('validated')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Validat</span>
                                            @break
                                        @case('obsolete')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Obsolet</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $article->createdBy?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $article->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('campus.help.articles.show', $article) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Veure">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('campus.help.articles.edit', $article) }}" 
                                           class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('campus.help.articles.toggle-status', $article) }}" 
                                              class="inline" onsubmit="return confirm('Vols canviar l\'estat d\'aquest article?')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Canviar estat">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('campus.help.articles.destroy', $article) }}" 
                                              class="inline" onsubmit="return confirm('Estàs segur de voler eliminar aquest article?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="mt-6">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-file-text text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No s'han trobat articles</h3>
                    <p class="text-gray-500 mb-4">No hi ha articles que coincideixin amb els filtres seleccionats.</p>
                    <a href="{{ route('campus.help.articles.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="bi bi-plus-circle mr-2"></i>Crear primer article
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
