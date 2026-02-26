@extends('campus.shared.layout')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="bi bi-cash-stack me-3"></i>
            {{ __('Tresoreria') }}
        </h1>
        <p class="text-gray-600">Gestió financera i pagaments del campus</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Gestión de Pagos --}}
        @can('campus.payments.view')
        <a href="#" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200 opacity-75 cursor-not-allowed"
           title="Funcionalitat en desenvolupament">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-emerald-100 rounded-lg mr-4">
                    <i class="bi bi-credit-card text-emerald-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Pagaments</h3>
                    <p class="text-sm text-gray-600">Gestionar pagaments de professors</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-clock me-1"></i>
                En desenvolupament
            </div>
        </a>
        @endcan
        
        {{-- Gestión de Profesores --}}
        @can('campus.teachers.view')
        <a href="{{ route('campus.teachers.index') }}" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-teal-100 rounded-lg mr-4">
                    <i class="bi bi-person-workspace text-teal-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Professors</h3>
                    <p class="text-sm text-gray-600">Gestió completa del professorat</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-arrow-right-circle me-1"></i>
                CRUD complet de professors
            </div>
        </a>
        @endcan
        
        {{-- Informes Financieros --}}
        @can('campus.reports.financial')
        <a href="#" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200 opacity-75 cursor-not-allowed"
           title="Funcionalitat en desenvolupament">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i class="bi bi-graph-up text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Informes</h3>
                    <p class="text-sm text-gray-600">Reports financers i estadístiques</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-clock me-1"></i>
                En desenvolupament
            </div>
        </a>
        @endcan
        
        {{-- Datos Financieros de Profesores --}}
        @can('campus.teachers.financial_data.view')
        <a href="{{ route('campus.treasury.teachers.index') }}" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="bi bi-bank text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Dades Financeres</h3>
                    <p class="text-sm text-gray-600">Informació bancària i fiscal</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Consultar dades econòmiques
            </div>
        </a>
        @endcan
        
        {{-- Consentimientos RGPD --}}
{{--         @can('campus.consents.view')
        <a href="{{ route('campus.treasury.teachers.rgpd.index') }}" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <i class="bi bi-shield-check text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900"> Consentiments RGPD</h3>
                    <p class="text-sm text-gray-600">Gestió de consentiments RGPD</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Veure i gestionar consentiments
            </div>
        </a>
        @endcan --}}
        

        
    </div>
    
    {{-- Estadísticas Existentes --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    {{-- Estadísticas según propuesta --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        {{-- Professors totals / RGPD acceptat --}}
        <div class="p-6 bg-white shadow rounded-lg">
            <div class="text-gray-500 text-sm mb-2">Professors totals / RGPD acceptat</div>
            <div class="text-3xl font-bold text-blue-600">
                {{ $data['teachers_total'] }} / {{ $data['teachers_with_rgpd'] }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Temporada {{ $data['season'] ?? '' }}</div>
        </div>

        {{-- Dades Bancàries --}}
        <div class="p-6 bg-white shadow rounded-lg">
            <div class="text-gray-500 text-sm mb-2">Dades Bancàries (Total / Actualitzades)</div>
            <div class="text-3xl font-bold text-green-600">
                {{ $data['course_assignments_total'] }} / {{ $data['course_assignments_updated'] }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Assignacions de cursos</div>
        </div>
    </div>

    {{-- Últimos Consentimientos RGPD --}}
    @if(isset($data['last_consents']) && count($data['last_consents']) > 0)
    <div class="mt-8 bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-4">Últims consentiments RGPD</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th>Professor</th>
                    <th>Temporada</th>
                    <th>Acceptat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['last_consents'] as $consent)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3">{{ $consent->teacher->name ?? '-' }}</td>
                        <td class="py-3">{{ $consent->season ?? '-' }}</td>
                        <td class="py-3">{{ $consent->accepted_at?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <a href="{{ route('campus.treasury.teachers.index') }}"
               class="text-blue-600 hover:underline inline-flex items-center">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Gestió professors (Tresoreria)
            </a>
        </div>
    </div>
    @endif
</div>

@endsection
