{{-- resources/views/campus/teachers/pdf-show.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('PDF de') . ' ' . $teacher->first_name . ' ' . $teacher->last_name)

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        {{-- Capçalera --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="bi bi-file-earmark-pdf mr-2"></i>
                    {{ __('PDF de') }} {{ $teacher->first_name }} {{ $teacher->last_name }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Fitxer:') }} {{ $pdf['filename'] }} | {{ __('Data:') }} {{ $pdf['modified_date'] }}
                </p>
                <div class="mt-2 text-xs text-gray-500">
                    <strong>{{ __('Teacher ID:') }}</strong> {{ $teacher->id }} | 
                    <strong>{{ __('Email:') }}</strong> {{ $teacher->user->email }}
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('campus.teachers.pdfs') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Tornar a Llista') }}
                </a>
                <a href="{{ route('campus.teachers.show', $teacher) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="bi bi-person mr-2"></i>
                    {{ __('Veure Perfil') }}
                </a>
            </div>
        </div>

        {{-- Informació del teacher --}}
        <div class="bg-gray-50 border-l-4 border-gray-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-person-circle text-gray-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-800">
                        {{ __('Informació del Teacher') }}
                    </h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <strong>{{ __('Nom:') }}</strong> {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </div>
                            <div>
                                <strong>{{ __('DNI:') }}</strong> {{ $teacher->dni ?? '-' }}
                            </div>
                            <div>
                                <strong>{{ __('IBAN:') }}</strong> {{ !empty($teacher->iban) ? '✅ Configurat' : '❌ No configurat' }}
                            </div>
                            <div>
                                <strong>{{ __('Cursos:') }}</strong> {{ $teacher->courses->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informació del PDF --}}
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-file-earmark-pdf text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        {{ __('Informació del PDF') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <strong>{{ __('Nom del fitxer:') }}</strong> 
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $pdf['filename'] }}</span>
                            </div>
                            <div>
                                <strong>{{ __('Mida:') }}</strong> {{ $pdf['size_formatted'] }}
                            </div>
                            <div>
                                <strong>{{ __('Data de modificació:') }}</strong> {{ $pdf['modified_date'] }}
                            </div>
                            <div>
                                <strong>{{ __('Ruta:') }}</strong> 
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">
                                    storage/app/consents/teachers/{{ $teacher->id }}/{{ $pdf['filename'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accions --}}
        <div class="flex justify-center space-x-4 mt-6">
            <a href="{{ route('campus.teachers.pdfs') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="bi bi-arrow-left mr-2"></i>
                {{ __('Tornar a Llista') }}
            </a>
            
            @if(auth()->user()->can('manage', $teacher))
                <a href="{{ route('teacher.profile.download', $pdf['filename']) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="bi bi-download mr-2"></i>
                    {{ __('Descarregar') }}
                </a>
            @else
                <div class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                    <i class="bi bi-download mr-2"></i>
                    {{ __('Descàrrega no permesa') }}
                </div>
            @endif
        </div>

        {{-- Informació addicional --}}
        <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-info-circle text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        {{ __('Informació Important') }}
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>{{ __('Aquesta vista mostra informació detallada del PDF sense descàrrega automàtica.') }}</li>
                            <li>{{ __('Els administradors poden descarregar el fitxer si tenen permisos.') }}</li>
                            <li>{{ __('Els teachers només poden veure la informació del PDF.') }}</li>
                            <li>{{ __('La ruta del fitxer és: storage/app/consents/teachers/{teacher_id}/{filename}') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
