@extends('campus.shared.layout')

@section('title', __('Detalls del Professor'))
@section('subtitle', $teacher->first_name . ' ' . $teacher->last_name)

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
                {{ __('Professors') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ $teacher->first_name }} {{ $teacher->last_name }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detalls del Professor</h1>
        <div class="flex space-x-4">
            <a href="{{ route('campus.teachers.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Tornar
            </a>
            @can('campus.teachers.edit')
                <a href="{{ route('campus.teachers.edit', $teacher) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Personal -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="mx-auto h-24 w-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-3xl font-bold mb-4">
                            {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $teacher->first_name }} {{ $teacher->last_name }}</h2>
                        <p class="text-gray-600">{{ $teacher->email }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="border-t pt-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Informació Personal</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">DNI:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->dni ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">IBAN:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->iban ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Titular del Compte:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->bank_titular ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Situació Fiscal:</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($teacher->fiscal_situation)
                                            {{ $teacher->fiscal_situation }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Títol Acadèmic:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->degree ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Especialització:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->specialization ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Títol Professional:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->title ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Data de Contractació:</dt>
                                    <dd class="text-sm text-gray-900">{{ $teacher->hiring_date ? $teacher->hiring_date->format('d/m/Y') : '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Estat:</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($teacher->status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $teacher->status == 'active' ? 'bg-green-100 text-green-800' : 
                                                ($teacher->status == 'inactive' ? 'bg-red-100 text-red-800' : 
                                                'bg-yellow-100 text-yellow-800') }}">
                                                {{ $teacher->status == 'active' ? 'Actiu' : 
                                                    ($teacher->status == 'inactive' ? 'Inactiu' : 'De Baixa') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="border-t pt-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Estat del Compte</h3>
                            @if($teacher->user)
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-1"></i>Actiu
                                    </span>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>Rol: <span class="font-medium">Professor</span></p>
                                        <p>ID Usuari: <span class="font-medium">{{ $teacher->user->id }}</span></p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="bi bi-times-circle mr-1"></i>Sense usuari
                                    </span>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>Rol: <span class="font-medium">Professor</span></p>
                                        <p>ID Usuari: <span class="font-medium">-</span></p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cursos Asignados -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Cursos Assignats</h2>
                    
                    @if($teacher->courses->count() > 0)
                        <div class="space-y-4">
                            @foreach($teacher->courses as $course)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $course->title }}</h3>
                                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-500">Codi:</span>
                                                    <span class="font-medium">{{ $course->code }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Temporada:</span>
                                                    <span class="font-medium">{{ $course->season->name ?? '-' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Hores:</span>
                                                    <span class="font-medium">{{ $course->pivot->hours_assigned ?? 0 }}h</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Rol:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $course->pivot->role == 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $course->pivot->role == 'teacher' ? 'Professor' : 'Assistent' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if($course->category)
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $course->category->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex space-x-2">
                                            <a href="{{ route('campus.courses.show', $course) }}" 
                                               class="text-blue-600 hover:text-blue-900" 
                                               title="Veure curs">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('campus.courses.edit')
                                                <a href="{{ route('campus.courses.edit', $course) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900" 
                                                   title="Editar curs">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumen -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $teacher->courses->count() }}</div>
                                    <div class="text-sm text-gray-600">Cursos Totals</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ $teacher->courses->sum('pivot.hours_assigned') }}</div>
                                    <div class="text-sm text-gray-600">Hores Totals</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ $teacher->courses->where('pivot.role', 'teacher')->count() }}
                                    </div>
                                    <div class="text-sm text-gray-600">Com a Professor</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="bi bi-book text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600">Aquest professor no té cursos assignats.</p>
                            @can('campus.teachers.edit')
                                <a href="{{ route('campus.teachers.edit', $teacher) }}" 
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                                    <i class="bi bi-plus mr-2"></i>Assignar Cursos
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
