{{-- resources/views/campus/shared/layout.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="bi bi-mortarboard me-2"></i>
                    {{ __('Campus Virtual') }} / @yield('title', __('Gestió'))
                </h2>
                <p class="text-sm text-gray-600 mt-1">@yield('subtitle', __('Administració del campus'))</p>
            </div>
            
            @hasSection('actions')
                <div class="relative z-10 flex space-x-2">
                    @yield('actions')
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            @hasSection('breadcrumbs')
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <i class="bi bi-house-door me-1"></i>
                                {{ __('Dashboard') }} 
                            </a>
                        </li>
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>
            @endif
            
            {{-- Alertas/Notificaciones --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            
            {{-- Contenido principal --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @yield('content') 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>