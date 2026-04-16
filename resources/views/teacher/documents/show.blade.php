@extends('layouts.app')

@section('title', $document->title)

@section('breadcrumbs')
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <a href="{{ route('teacher.documents.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-gray-700">
        Meus Documents
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
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <i class="{{ $document->file_icon }} text-3xl text-gray-400 mr-4"></i>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h1>
                            <p class="text-sm text-gray-500">
                                Creat el {{ $document->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($document->description)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Descripció</h3>
                            <p class="text-gray-600">{{ $document->description }}</p>
                        </div>
                    @endif

                    <!-- Metadatos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @if($document->course)
                            <div class="flex items-center">
                                <i class="bi bi-book text-blue-500 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Curs</p>
                                    <p class="text-sm font-medium">{{ $document->course->title }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center">
                            <i class="bi bi-tag text-green-500 mr-2"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tipus</p>
                                <p class="text-sm font-medium">{{ $document->document_type_label }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <i class="bi bi-eye text-purple-500 mr-2"></i>
                            <div>
                                <p class="text-xs text-gray-500">Visibilitat</p>
                                <p class="text-sm font-medium">{{ $document->student_visibility_label }}</p>
                            </div>
                        </div>

                        @if($document->academic_year)
                            <div class="flex items-center">
                                <i class="bi bi-calendar text-orange-500 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Any Acadèmic</p>
                                    <p class="text-sm font-medium">{{ $document->academic_year }}-{{ $document->academic_year + 1 }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center">
                            <i class="bi bi-file-earmark text-gray-500 mr-2"></i>
                            <div>
                                <p class="text-xs text-gray-500">Mida</p>
                                <p class="text-sm font-medium">{{ $document->formatted_file_size }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <i class="bi bi-download text-indigo-500 mr-2"></i>
                            <div>
                                <p class="text-xs text-gray-500">Descàrregues</p>
                                <p class="text-sm font-medium">{{ $document->download_count }}</p>
                            </div>
                        </div>
                    </div>

                    @if($document->tags)
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Etiquetes</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $document->tags) as $tag)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Acciones -->
                <div class="flex space-x-2 ml-6">
                    <a href="{{ route('teacher.documents.download', $document) }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="bi bi-download mr-2"></i>
                        Descarregar
                    </a>
                    <a href="{{ route('teacher.documents.edit', $document) }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="bi bi-pencil mr-2"></i>
                        Editar
                    </a>
                    <form action="{{ route('teacher.documents.destroy', $document) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50"
                                onclick="return confirm('Estás seguro de eliminar este documento?')">
                            <i class="bi bi-trash mr-2"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Descargas Recientes -->
        @if($recentDownloads->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="bi bi-clock-history text-blue-500 mr-2"></i>
                    Descàrregues Recents
                </h2>
                
                <div class="space-y-3">
                    @foreach($recentDownloads as $download)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="bi bi-person text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $download->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $download->downloaded_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $download->ip_address }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
