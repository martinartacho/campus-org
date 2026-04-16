@extends('layouts.app')

@section('title', 'Meus Documents')

@section('breadcrumbs')
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-1 text-sm font-medium text-gray-500">{{ __('Meus Documents') }}</span>
</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header con estadísticas -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="bi bi-journal-text text-green-600 mr-3"></i>
                    Meus Documents
                </h1>
                <p class="text-gray-600">
                    Gestiona els teus documents i materials educatius
                </p>
            </div>
            <a href="{{ route('teacher.documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <i class="bi bi-plus-circle mr-2"></i>
                Nou Document
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border">
                <div class="text-sm text-gray-600">Total Documents</div>
                <div class="text-2xl font-bold text-blue-700">{{ $stats['total_documents'] ?? 0 }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border">
                <div class="text-sm text-gray-600">Per Curs</div>
                <div class="text-2xl font-bold text-green-700">{{ $stats['course_documents'] ?? 0 }}</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border">
                <div class="text-sm text-gray-600">Materials</div>
                <div class="text-2xl font-bold text-purple-700">{{ $stats['material_documents'] ?? 0 }}</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border">
                <div class="text-sm text-gray-600">Tasques</div>
                <div class="text-2xl font-bold text-orange-700">{{ $stats['task_documents'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <form method="GET" action="{{ route('teacher.documents.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cercar</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Títol, descripció, etiquetes..."
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="bi bi-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Curso -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Curs</label>
                    <select name="course" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tots els cursos</option>
                        @foreach($teacherCourses as $courseTitle => $courseId)
                            <option value="{{ $courseId }}" {{ request('course') == $courseId ? 'selected' : '' }}>
                                {{ $courseTitle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo de Documento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipus</label>
                    <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tots els tipus</option>
                        <option value="material" {{ request('document_type') == 'material' ? 'selected' : '' }}>Material</option>
                        <option value="tarea" {{ request('document_type') == 'tarea' ? 'selected' : '' }}>Tasca</option>
                        <option value="evaluacion" {{ request('document_type') == 'evaluacion' ? 'selected' : '' }}>Avaluació</option>
                        <option value="recurso" {{ request('document_type') == 'recurso' ? 'selected' : '' }}>Recurs</option>
                    </select>
                </div>

                <!-- Año Académico -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Any Acadèmic</label>
                    <select name="academic_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tots els anys</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}-{{ $year + 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="bi bi-funnel mr-2"></i>
                    Filtrar
                </button>
                <a href="{{ route('teacher.documents.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="bi bi-x-circle mr-2"></i>
                    Netejar
                </a>
            </div>
        </form>
    </div>

    <!-- Lista de Documentos -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                Documents ({{ $documents->total() }})
            </h2>
        </div>

        @if($documents->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($documents as $document)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="{{ $document->file_icon }} text-2xl text-gray-400 mr-3"></i>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('teacher.documents.show', $document) }}" class="hover:text-green-600">
                                                {{ $document->title }}
                                            </a>
                                        </h3>
                                        @if($document->course)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $document->course->title }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">
                                            {{ $document->document_type_label }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                            {{ $document->student_visibility_label }}
                                        </span>
                                    </div>
                                </div>
                                @if($document->description)
                                    <p class="mt-1 text-sm text-gray-600">{{ Str::limit($document->description, 100) }}</p>
                                @endif
                                <div class="mt-2 flex items-center text-xs text-gray-500">
                                    <span>{{ $document->formatted_file_size }}</span>
                                    <span class="mx-2">·</span>
                                    <span>{{ $document->created_at->format('d/m/Y H:i') }}</span>
                                    @if($document->download_count > 0)
                                        <span class="mx-2">·</span>
                                        <span>{{ $document->download_count }} descàrregues</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('teacher.documents.download', $document) }}" class="text-green-600 hover:text-green-800">
                                    <i class="bi bi-download"></i>
                                </a>
                                <a href="{{ route('teacher.documents.edit', $document) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('teacher.documents.destroy', $document) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Estás seguro de eliminar este documento?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $documents->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="bi bi-file-earmark text-4xl text-gray-300"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hi ha documents</h3>
                <p class="mt-1 text-sm text-gray-500">Comença creant el teu primer document.</p>
                <div class="mt-6">
                    <a href="{{ route('teacher.documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="bi bi-plus-circle mr-2"></i>
                        Crear Document
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
