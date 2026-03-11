@extends('campus.shared.layout')

@section('title', __('Nou Professor/a'))
@section('subtitle', __('Crear nou professor/a'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.dashboard')
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('Professorat') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('Nou Professor/a') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Nou Professor/a</h1>
        <a href="{{ route('campus.teachers.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Tornar
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <form action="{{ route('campus.teachers.store') }}" method="POST">
                @csrf
                
                <!-- Datos Personales -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">Dades Personals</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Cognoms <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telèfon
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">
                                DNI
                            </label>
                            <input type="text" 
                                   id="dni" 
                                   name="dni" 
                                   value="{{ old('dni') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('dni')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="iban" class="block text-sm font-medium text-gray-700 mb-2">
                                IBAN
                            </label>
                            <input type="text" 
                                   id="iban" 
                                   name="iban" 
                                   value="{{ old('iban') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('iban')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_titular" class="block text-sm font-medium text-gray-700 mb-2">
                                Titular del Compte
                            </label>
                            <input type="text" 
                                   id="bank_titular" 
                                   name="bank_titular" 
                                   value="{{ old('bank_titular') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('bank_titular')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fiscal_situation" class="block text-sm font-medium text-gray-700 mb-2">
                                Situació Fiscal
                                <span class="text-xs text-gray-500 ml-2">(Valores vàlids: autonomo, empleat, pensionista, altres)</span>
                            </label>
                            <input type="text" 
                                   id="fiscal_situation" 
                                   name="fiscal_situation" 
                                   value="{{ old('fiscal_situation') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Introdueix la situació fiscal...">
                            @error('fiscal_situation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- <div>
                            <label for="degree" class="block text-sm font-medium text-gray-700 mb-2">
                                Títol Acadèmic
                            </label>
                            <input type="text" 
                                   id="degree" 
                                   name="degree" 
                                   value="{{ old('degree') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('degree')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> -->

                            <!-- <div>
                                <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                                    Especialització
                                </label>
                                <input type="text" 
                                    id="specialization" 
                                    name="specialization" 
                                    value="{{ old('specialization') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('specialization')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
 -->
                        <!-- <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Títol Professional
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="Dr., Prof., etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> -->

                        <div>
                            <label for="hiring_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Data d'alta
                            </label>
                            <input type="date" 
                                   id="hiring_date" 
                                   name="hiring_date" 
                                   value="{{ old('hiring_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('hiring_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Estat
                            </label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Actiu</option>
                                <option value="inactive">Inactiu</option>
                                <option value="on_leave">De Baixa</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Adreça
                            </label>
                            <input type="text" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Codi Postal
                            </label>
                            <input type="text" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Ciutat
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- <div>
                            <label for="areas" class="block text-sm font-medium text-gray-700 mb-2">
                                Àrees d'Especialització
                            </label>
                            <textarea 
                                   id="areas" 
                                   name="areas" 
                                   rows="3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Introduïu les àrees d'especialització separades per comes...">{{ old('areas') }}</textarea>
                            @error('areas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> -->
                    </div>
                </div>

                <!-- Asignación de Cursos -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">Assignació de Cursos</h2>
                    <div id="courses-container" class="space-y-4">
                        <div class="course-item bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Curs
                                    </label>
                                    <div class="flex space-x-2">
                                        <select name="courses[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Seleccionar curs...</option>
                                            <!-- <option value="new">+ Crear nou curs...</option> -->
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Sessions Assignades
                                    </label>
                                    <input type="number" 
                                           name="sessions_assigned[]" 
                                           min="0" 
                                           step="1"
                                           placeholder="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Rol
                                    </label>
                                    <select name="role[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="teacher">Professor/a</option>
                                        <option value="assistant">Assistent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                                    </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('campus.teachers.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Cancel·lar
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-save mr-2"></i>Crear Professor/a
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

