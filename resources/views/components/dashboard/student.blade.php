{{-- Dashboard Estudiant --}}
<div class="space-y-6">
    {{-- Benvinguda --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            {{ __('Benvingut/da') }}, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-600">{{ __('Aquí tens la teva informació acadèmica') }}</p>
    </div>

    {{-- Estadístiques de l'Estudiant --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="bi bi-book text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('Cursos Matriculats') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['total_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="bi bi-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('Cursos Actius') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['active_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                    <i class="bi bi-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('Cursos Pendents') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['pending_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="bi bi-trophy text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('Cursos Completats') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['completed_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Llista de Cursos Matriculats --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Els Meus Cursos') }}</h2>
        </div>
        <div class="p-6">
            @if(isset($studentCourses) && $studentCourses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($studentCourses as $registration)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="font-semibold text-gray-900">{{ $registration->course->title }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($registration->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($registration->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="bi bi-tag mr-2"></i>
                                    <span>{{ $registration->course->code }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-calendar mr-2"></i>
                                    <span>{{ $registration->course->start_date }} - {{ $registration->course->end_date }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-geo-alt mr-2"></i>
                                    <span>{{ $registration->course->location ?? 'Online' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-currency-euro mr-2"></i>
                                    <span>€{{ number_format($registration->amount, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <a href="{{ route('campus.courses.show', $registration->course->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    {{ __('Veure Detalls') }} →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-book text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600">{{ __('Encara no tés cap matrícula activa') }}</p>
                    <a href="{{ route('campus.courses.index') }}" 
                       class="mt-3 inline-flex items-center text-blue-600 hover:text-blue-800">
                        <i class="bi bi-search mr-2"></i>
                        {{ __('Explorar Cursos') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Accions Ràpides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('campus.courses.index') }}" 
           class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="bi bi-search text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ __('Explorar Cursos') }}</p>
                    <p class="text-sm text-gray-600">{{ __('Descobreix nous cursos') }}</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('profile.edit') }}" 
           class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="bi bi-person text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ __('Perfil') }}</p>
                    <p class="text-sm text-gray-600">{{ __('Actualitza les teves dades') }}</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('notifications.index') }}" 
           class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="bi bi-bell text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ __('Notificacions') }}</p>
                    <p class="text-sm text-gray-600">{{ __('Revisa les teves notificacions') }}</p>
                </div>
            </div>
        </a>
    </div>
</div>