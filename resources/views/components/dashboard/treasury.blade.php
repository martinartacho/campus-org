@props([
    'stats' => [],
    'debug' => null,
    'error' => null,
])

<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">
       {{ __('campus.treasury_manager') }} 
    </h3>
    @if(config('app.debug'))
        <pre class="bg-gray-100 p-3 text-xs rounded border">{{ var_export([
            'error' => $error,
            'debug' => $debug,
            'stats' => $stats,
        ], true) }}
        </pre>
    @endif
    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200 hover:border-green-300">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-green-800">{{ __('Estadístiques') }}</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
            @isset($stats['teachers_total'])
                <span class="text-green-700">{{ __('campus.teachers') }}: {{ $stats['teachers_total'] }}</span>
            @endisset

            @isset($stats['teachers_with_rgpd'])
                <span class="text-green-700">RGPD acceptats: {{ $stats['teachers_with_rgpd'] }}</span>
            @endisset

            @isset($stats['pending_bank_data'])
                <span class="text-green-700">Dades bancàries pendents: {{ $stats['pending_bank_data'] }}</span>
            @endisset

            @isset($stats['course_assignments_total'])
                <span class="text-green-700">Assignacions totals: {{ $stats['course_assignments_total'] }}</span>
            @endisset
        </div>
    </div>
    
    {{-- Estadísticas de PDFs --}}
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300 mb-6">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-blue-800">{{ __('Estadístiques de PDFs') }}</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
            @isset($stats['teachers_with_pdfs'])
                <span class="text-blue-700">Amb PDFs: {{ $stats['teachers_with_pdfs'] }}</span>
            @endisset

            @isset($stats['teachers_without_pdfs'])
                <span class="text-blue-700">Sense PDFs: {{ $stats['teachers_without_pdfs'] }}</span>
            @endisset

            @isset($stats['teachers_without_iban'])
                <span class="text-blue-700">Sense IBAN: {{ $stats['teachers_without_iban'] }}</span>
            @endisset

            @isset($stats['teachers_total'])
                <span class="text-blue-700">Total: {{ $stats['teachers_total'] }}</span>
            @endisset
        </div>
    </div>
</div> 

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- DADES BANCÀRIES PROFESSORAT --}}
        @can('campus.payments.view')
            <a href="{{ route('campus.teachers.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Dades bancàries professorat</p>
                            <p class="text-2xl font-bold text-blue-900">
                                @isset($stats['course_assignments_total'])
                                {{ $stats['course_assignments_total'] }}
                                @endisset
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="bi bi-credit-card text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-green-700">Pendents: {{ $stats['pending_bank_data'] ?? 0 }}</span>
                        <span class="text-green-700">Completats: {{ $stats['course_assignments_updated'] ?? 0 }}</span>
                    </div>
                    
                    <div class="mt-3 pt-2 border-t border-blue-200">
                        <span class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                            Gestionar dades bancàries <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan
        
        {{-- PROFESSORAT --}}
        @can('campus.teachers.view')
            <a href="{{ route('campus.teachers.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-800">{{ __('campus.teachers') }}</p>
                            <p class="text-2xl font-bold text-purple-900">
                                @isset($stats['teachers_total'])
                                {{ $stats['teachers_total'] }}
                                @endisset
                            </p>
                        </div>
                        <div class="p-2 bg-purple-200 rounded-lg">
                            <i class="bi bi-people text-purple-600 text-xl"></i>
                        </div>
                    </div>
                
                    <div class="mt-3 pt-2 border-t border-purple-200">
                        <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                            Gestionar Professorat<i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan

        {{-- CONFIGURACIÓ PDF --}}
        @can('settings.edit')
            <a href="{{ route('treasury.settings') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200 hover:border-orange-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-800">Configuració PDF</p>
                            <p class="text-2xl font-bold text-orange-900">
                                <i class="bi bi-gear"></i>
                            </p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="bi bi-file-earmark-pdf text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-2 border-t border-orange-200">
                        <span class="text-xs text-orange-600 hover:text-orange-800 flex items-center">
                            Configurar dates límit <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan

        {{-- DOCUMENTS PDF --}}
        @can('campus.teachers.view')
            <a href="{{ route('campus.teachers.pdfs') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg border border-red-200 hover:border-red-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">Documents PDF</p>
                            <p class="text-2xl font-bold text-red-900">
                                @isset($stats['teachers_with_pdfs'])
                                {{ $stats['teachers_with_pdfs'] }}
                                @endisset
                            </p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="bi bi-file-earmark-pdf text-red-600 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-green-700">Amb PDFs: {{ $stats['teachers_with_pdfs'] ?? 0 }}</span>
                        <span class="text-red-700">Sense PDFs: {{ $stats['teachers_without_pdfs'] ?? 0 }}</span>
                    </div>
                    
                    <div class="mt-3 pt-2 border-t border-red-200">
                        <span class="text-xs text-red-600 hover:text-red-800 flex items-center">
                            Accedir a documents <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan
    </div>
</div>
