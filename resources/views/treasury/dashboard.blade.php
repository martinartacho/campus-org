@extends('campus.shared.layout')

@section('title', __('campus.treasury_dashboard'))
@section('subtitle', __('campus.treasury_dashboard'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header con estadísticas --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="bi bi-shield-check text-green-600 mr-3"></i>
                    {{ __('campus.treasury_dashboard') }}
                </h1>
                <div class="text-sm text-gray-500">
                    {{ __('campus.treasury_management') }}
                </div>
            </div>
            
            {{-- Estadísticas principales --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Professors totals / RGPD acceptat --}}
                <div class="bg-white p-6 rounded-lg shadow text-center lg:col-span-2">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $stats['total_teachers'] ?? 0 }} / {{ $stats['teachers_with_consent'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">Professors totals / RGPD acceptat</div>
                </div>
                
                {{-- Dades Bancaries --}}
                <div class="bg-white p-6 rounded-lg shadow text-center lg:col-span-2">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $stats['total_course_assignments'] ?? 0 }} / {{ $stats['updated_course_assignments'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">Dades Bancaries (Total / Actualitzades)</div>
                    <div class="text-xs text-gray-400 mt-1">Temporada actual</div>
                </div>
                
                {{-- Aquest mes (opcional) --}}
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['this_month_consents'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">{{ __('campus.this_month_consents') }}</div>
                </div>
            </div>
        </div>

        {{-- Filtros y búsqueda --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">{{ __('campus.consents_list') }}</h2>
                
                {{-- Filtros --}}
                <div class="flex gap-4">
                    <form method="GET" action="{{ route('treasury.dashboard') }}" class="flex gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('campus.filter_season') }}</label>
                            <select name="season" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('campus.all_seasons') }}</option>
                                {{-- Aquí se agregarían las temporadas dinámicamente --}}
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('campus.filter_status') }}</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('campus.all_status') }}</option>
                                <option value="completed">{{ __('campus.completed') }}</option>
                                <option value="pending">{{ __('campus.pending') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('campus.search') }}</label>
                            <input type="text" name="search" value="{{ request('search', '') }}" 
                                   placeholder="{{ __('campus.search_placeholder') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                {{ __('campus.search') }}
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="flex gap-4">
                    <a href="{{ route('treasury.consents.export') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="bi bi-download mr-2"></i>
                        {{ __('campus.export_excel') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Lista de consentimientos --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($consentments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.teacher') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.course') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.season') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.accepted_at') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('campus.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($consentments as $consent)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <div class="text-sm font-medium leading-none text-gray-600">
                                                    {{ strtoupper(substr($consent->teacher->user->first_name ?? '', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $consent->teacher->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $consent->teacher->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $consent->course->title ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $consent->course->code ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $consent->season ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $consent->accepted_at?->format('d/m/Y H:i') ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($consent->document_path)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✅ {{ __('campus.completed') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                📝 {{ __('campus.pending') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2">
                                            @if($consent->document_path)
                                                <a href="{{ route('consents.download', $consent) }}" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    <i class="bi bi-download mr-1"></i>
                                                    {{ __('campus.download_pdf') }}
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('treasury.consents.show', $consent) }}" 
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                <i class="bi bi-eye mr-1"></i>
                                                {{ __('campus.view_details') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div class="mt-6">
                    {{ $consentments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <i class="bi bi-inbox text-4xl mb-4"></i>
                        <p class="text-xl font-medium">{{ __('campus.no_consents_found') }}</p>
                        <p class="text-sm text-gray-600 mt-2">{{ __('campus.no_consents_description') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
