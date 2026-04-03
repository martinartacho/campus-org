{{-- resources/views/teacher/profile/pdf-download.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('Descàrrega de PDFs'))

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        {{-- Capçalera --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="bi bi-file-earmark-pdf mr-2"></i>
                    {{ __('Descàrrega de PDFs') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Llista de PDFs generats. Fes clic per descarregar. Màxim 3 fitxers.') }}
                </p>
            </div>
            <a href="{{ route('teacher.profile') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="bi bi-arrow-left mr-2"></i>
                {{ __('Tornar al Perfil') }}
            </a>
        </div>

        {{-- Llista de PDFs --}}
        @if($allPdfs && count($allPdfs) > 0)
            <div class="space-y-4">
                @foreach($allPdfs as $pdf)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="bi bi-file-earmark-pdf text-red-500 text-xl mr-3"></i>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ __('Fitxer PDF') }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ __('Nom del fitxer:') }} <span class="font-mono">{{ $pdf['filename'] }}</span>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ __('Data de modificació:') }} {{ $pdf['modified_date'] }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ __('Mida:') }} {{ $pdf['size_formatted'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                {{-- Indicador d'estat del PDF --}}
                                <x-teacher-pdf-status :teacher="$teacher" />
                                
                                {{-- Botó de descàrrega --}}
                                <a href="{{ route('teacher.profile.download', $pdf['filename']) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    <i class="bi bi-download mr-2"></i>
                                    {{ __('Descarregar') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- No hi ha PDFs --}}
            <div class="text-center py-12">
                <i class="bi bi-file-earmark-pdf text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ __('No hi ha PDFs disponibles') }}
                </h3>
                <p class="text-gray-600 mb-6">
                    {{ __('Encara no has generat cap PDF. Genera un PDF des del teu perfil per veure\'l aquí.') }}
                </p>
                <a href="{{ route('teacher.profile') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="bi bi-plus-circle mr-2"></i>
                    {{ __('Generar PDF') }}
                </a>
            </div>
        @endif

        {{-- Informació addicional --}}
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        {{ __('Informació Important') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>{{ __('Els PDFs es guarden automàticament quan generes el teu consentiment de dades.') }}</li>
                            <li>{{ __('Es mantenen un màxim de 3 PDFs per teacher.') }}</li>
                            <li>{{ __('Els PDFs més antics s\'eliminen automàticament.') }}</li>
                            <li>{{ __('Si tens problemes amb la descàrrega, contacta amb administració.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
