@extends('campus.shared.layout')

@section('title', 'Importar Registros')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Importar Registros</h1>
        <a href="{{ url('/campus/registrations') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ url('/campus/registrations-import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Archivo de Importación
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Subir archivo</span>
                            <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv">
                        </label>
                        <p class="pl-1">o arrastra y suelta</p>
                    </div>
                    <p class="text-xs text-gray-500">XLSX, XLS, CSV hasta 10MB</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">Instrucciones</h3>
                <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                    <li>El archivo debe contener múltiples hojas (Orders, TDAH, INTEL·LIGÈNCIA EMOCIONAL, etc.)</li>
                    <li>El sistema detectará automáticamente el código del curso según el nombre de la hoja</li>
                    <li>Se crearán automáticamente los alumnos que no existan</li>
                    <li>Los registros se marcarán como confirmados y pagados</li>
                    <li>Formato esperado: NIF, Nombre, Email, Teléfono, Precio</li>
                </ul>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i>Importar Registros
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
