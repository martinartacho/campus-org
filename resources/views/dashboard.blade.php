{{-- resources\views\dashboard.blade.php --}}
@php
    $context = $context ?? session('context');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('site.Dashboard') }} 
        </h2>
    </x-slot>

    {{-- Selector de roles (temporal para pruebas) --}}
    @if(auth()->check() && auth()->user()->roles->count() > 1)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-blue-900">Rol Activo: {{ ucfirst(session('active_role', auth()->user()->roles->first()->name)) }} </h3>
                <p class="text-xs text-blue-700">Tienes {{ auth()->user()->roles->count() }} roles disponibles</p>
            </div>
            <p class="text-xs text-blue-700"></p>
            <div class="flex space-x-2">
                @foreach(auth()->user()->roles as $role)
                    <a href="{{ route('dashboard.switch.role', $role->name) }}" 
                       class="px-3 py-1 text-xs rounded @if(session('active_role') == $role->name) bg-blue-600 text-white @else bg-white text-blue-600 border border-blue-300 @endif hover:bg-blue-100">
                        {{ ucfirst($role->name) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Usar rol activo si existe, sino verificar todos los roles --}}
            @php
                $activeRole = session('active_role');
                $user = auth()->user();
            @endphp

            {{-- 1. Dashboard Admin --}}
            @if(($activeRole && in_array($activeRole, ['admin', 'super-admin']) && $user->hasRole($activeRole)) || (!$activeRole && $user->hasAnyRole(['admin', 'super-admin'])))
                <x-dashboard.admin :stats="$stats ?? []" />

            {{-- 2. Dashboard Manager --}}
            @elseif(($activeRole && in_array($activeRole, ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor']) && $user->hasRole($activeRole)) || (!$activeRole && $user->hasAnyRole(['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'])))
                <!-- <x-dashboard.manager :stats="$stats ?? []" /> -->
                <x-dashboard.admin-cards :stats="$stats ?? []" :active-role="$activeRole" />
                @foreach($widgets ?? [] as $widget)
                    @include($widget)
                @endforeach
                
                {{-- Estadístiques del Sistema para coordinacio --}}
                @if($activeRole === 'coordinacio')
                    <x-dashboard.system-stats :stats="$stats ?? []" />
                @endif

            {{-- 2.1 Dashboard Treasury --}}
            @elseif(($activeRole && $activeRole === 'treasury' && $user->hasRole($activeRole)) || (!$activeRole && $user->hasRole('treasury')))
                <x-dashboard.treasury :stats="$data['stats'] ?? []" />

            {{-- 3. Teacher --}}
            @elseif(($activeRole && $activeRole === 'teacher' && $user->hasRole($activeRole)) || (!$activeRole && $user->hasRole('teacher')))
                <x-dashboard.teacher
                    :teacher="$teacher ?? null"
                    :teacher-courses="$teacherCourses ?? collect()"
                    :stats="$stats ?? []"
                />

            {{-- 4. Student --}}
            @elseif(($activeRole && $activeRole === 'student' && $user->hasRole($activeRole)) || (!$activeRole && $user->hasRole('student')))
                <x-dashboard.student
                    :student="$student ?? null"
                    :studentStats="$studentStats ?? []"
                    :studentCourses="$studentCourses ?? collect()"
                    :recentActivity="$recentActivity ?? collect()"
                    :upcomingClasses="$upcomingClasses ?? collect()"
                    :grades="$grades ?? collect()"
                    :debug="$debug ?? null"
                    :error="$error ?? null"
                />

            {{-- 5. Fallback --}}
            @else
                {{-- Dashboard por defecto si no coincide ningún rol --}}
                <div class="text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Dashboard no disponible') }}</h3>
                    <p class="mt-2 text-gray-500">{{ __('Contacta amb l\'administrador') }}</p>
                </div>
            @endif
               
        </div>
    </div>
</x-app-layout>