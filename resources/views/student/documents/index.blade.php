@extends('campus.shared.layout')

@section('title', 'Documents dels Meus Cursos')

@section('breadcrumbs')
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-1 text-sm font-medium text-gray-500">{{ __('Documents dels Meus Cursos') }}</span>
</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="bi bi-journal-text text-blue-600 mr-3"></i>
                Documents dels Meus Cursos
            </h1>
            <p class="text-gray-600">
                Accedeix als materials dels teus cursos i als documents públics
            </p>
        </div>

        <!-- Filtros -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('student.documents.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-search"></i> Cercar
                        </label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Títol, descripció...">
                    </div>

                    <!-- Curso -->
                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-book"></i> Curs
                        </label>
                        <select id="course" 
                                name="course" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tots els meus cursos</option>
                            @foreach($studentCourses as $course)
                                <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Documento -->
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-file-earmark"></i> Tipus
                        </label>
                        <select id="document_type" 
                                name="document_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tots els tipus</option>
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $value }}" {{ request('document_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="bi bi-funnel mr-2"></i>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'course', 'document_type']))
                        <a href="{{ route('student.documents.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="bi bi-x-circle mr-2"></i>
                            Netejar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_documents'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Documents</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['course_documents'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Dels Meus Cursos</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['public_documents'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Públics</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['recent_downloads'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Descàrregues (7d)</div>
            </div>
        </div>

        <!-- Lista de Documentos -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    Documents Disponibles
                    <span class="ml-2 text-sm text-gray-500">({{ $documents->count() }} documents)</span>
                </h2>
            </div>

            @if($documents->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($documents as $document)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <!-- Información del documento -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <i class="{{ $document->file_icon }} text-2xl text-gray-400 mr-3"></i>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-medium text-gray-900 truncate">
                                                {{ $document->title }}
                                            </h3>
                                            @if($document->description)
                                                <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                                    {{ $document->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Metadatos -->
                                    <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                        @if($document->course)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="bi bi-book mr-1"></i>
                                                {{ $document->course->title }}
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="bi bi-tag mr-1"></i>
                                            {{ $document->document_type_label }}
                                        </span>

                                        @if($document->teacher)
                                            <span class="inline-flex items-center">
                                                <i class="bi bi-person mr-1"></i>
                                                {{ $document->teacher->name }}
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center">
                                            <i class="bi bi-calendar mr-1"></i>
                                            {{ $document->created_at->format('d/m/Y') }}
                                        </span>

                                        <span class="inline-flex items-center">
                                            <i class="bi bi-hdd mr-1"></i>
                                            {{ $document->formatted_file_size }}
                                        </span>

                                        <span class="inline-flex items-center">
                                            <i class="bi bi-download mr-1"></i>
                                            {{ $document->download_count }} descàrregues
                                        </span>
                                    </div>

                                    @if($document->tags)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach(explode(',', $document->tags) as $tag)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                                    #{{ trim($tag) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Acciones -->
                                <div class="ml-6 flex-shrink-0">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('student.documents.download', $document) }}" 
                                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                            <i class="bi bi-download mr-2"></i>
                                            Descarregar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($documents->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $documents->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-12 text-center">
                    <i class="bi bi-journal-x text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No s'han trobat documents</h3>
                    <p class="text-gray-500">
                        No hi ha documents disponibles per als teus cursos en aquest moment.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
