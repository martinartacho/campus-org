@extends('campus.shared.layout')

@section('title', $document->title)

@section('breadcrumbs')
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <a href="{{ route('student.documents.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-gray-700">
        Documents
    </a>
</li>
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-1 text-sm font-medium text-gray-500">{{ $document->title }}</span>
</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="bi bi-house-door mr-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
                            <a href="{{ route('student.documents.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600">
                                Documents
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
                            <span class="ml-1 text-sm font-medium text-gray-500">{{ $document->title }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="{{ $document->file_icon }} text-blue-600 mr-3"></i>
                        {{ $document->title }}
                    </h1>
                    @if($document->description)
                        <p class="text-lg text-gray-600">{{ $document->description }}</p>
                    @endif
                </div>
                
                <a href="{{ route('student.documents.download', $document) }}" 
                   class="inline-flex items-center px-6 py-3 text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg">
                    <i class="bi bi-download mr-2"></i>
                    Descarregar
                </a>
            </div>
        </div>

        <!-- Información Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Detalles del Documento -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informació del Document</h2>
                    
                    <div class="space-y-4">
                        <!-- Descripción -->
                        @if($document->description)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Descripció</h3>
                                <p class="text-gray-600">{{ $document->description }}</p>
                            </div>
                        @endif

                        <!-- Etiquetas -->
                        @if($document->tags)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Etiquetes</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(explode(',', $document->tags) as $tag)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                            #{{ trim($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Información del Archivo -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Informació de l'Arxiu</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex items-center">
                                        <i class="bi bi-file-earmark mr-2 text-gray-400"></i>
                                        <span class="text-gray-600">Nom:</span>
                                        <span class="ml-2 font-medium">{{ $document->file_name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="bi bi-hdd mr-2 text-gray-400"></i>
                                        <span class="text-gray-600">Mida:</span>
                                        <span class="ml-2 font-medium">{{ $document->formatted_file_size }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="bi bi-calendar mr-2 text-gray-400"></i>
                                        <span class="text-gray-600">Data:</span>
                                        <span class="ml-2 font-medium">{{ $document->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="bi bi-download mr-2 text-gray-400"></i>
                                        <span class="text-gray-600">Descàrregues:</span>
                                        <span class="ml-2 font-medium">{{ $document->download_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Contextual -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Context Educatiu</h2>
                    
                    <div class="space-y-4">
                        <!-- Curso -->
                        @if($document->course)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Curs</h3>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="bi bi-book text-blue-600 mr-2"></i>
                                        <div>
                                            <div class="font-medium text-blue-900">{{ $document->course->title }}</div>
                                            <div class="text-sm text-blue-700">{{ $document->course->code }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Profesor -->
                        @if($document->teacher)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Professor/a</h3>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="bi bi-person text-green-600 mr-2"></i>
                                        <div>
                                            <div class="font-medium text-green-900">{{ $document->teacher->name }}</div>
                                            <div class="text-sm text-green-700">{{ $document->teacher->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tipo de Documento -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Tipus de Document</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <i class="bi bi-tag mr-1"></i>
                                {{ $document->document_type_label }}
                            </span>
                        </div>

                        <!-- Visibilidad -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Visibilitat</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="bi bi-eye mr-1"></i>
                                {{ $document->student_visibility_label }}
                            </span>
                        </div>

                        <!-- Año Académico -->
                        @if($document->academic_year)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Any Acadèmic</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i class="bi bi-calendar3 mr-1"></i>
                                    {{ $document->academic_year }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Descargas Recientes -->
        @if($recentDownloads && $recentDownloads->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Activitat Recent</h2>
                <div class="space-y-3">
                    @foreach($recentDownloads as $download)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="bi bi-person text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $download->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $download->downloaded_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $download->ip_address }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
