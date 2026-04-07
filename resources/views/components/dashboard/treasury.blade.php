@props([
    'stats' => [],
    'debug' => null,
    'error' => null,
])

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="bi bi-cash-stack me-3"></i>
            {{ __('Tresoreria') }}
        </h1>
        <p class="text-gray-600">Gestió financera i pagaments del campus</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    
        @can('settings.edit')
        {{-- Targeta de Configuració PDF --}}
        <div class="bg-white p-4 rounded shadow text-center hover:shadow-lg transition-shadow cursor-pointer"
            onclick="window.location.href='{{ route('treasury.settings') }}'">
            <div class="text-3xl font-bold text-blue-600 mb-2">
                <i class="bi bi-gear"></i>
            </div>
            <div class="text-sm font-semibold text-gray-800 mb-2">
                {{ __('Configuració dataPDF') }}
            </div>
            <div class="text-xs text-gray-500">
                {{ __('Data límit: ') }}{{ \App\Models\Setting::get('pdf_update_deadline', '2026-03-15') }}
            </div>
              <div class="text-xs text-gray-500">
                {{ __('Periode congelat: ') }}
                {{ \App\Models\Setting::get('payment_freeze_start', '2026-03-20') }} 
                - 
                {{ \App\Models\Setting::get('payment_freeze_end', '2026-04-25') }}
            </div>

        </div>
        @endcan


       <!--  https://campus.upg.cat/campus/teachers/pdfs -->
        
        {{-- Gestión de PDFs --}}
        @can('campus.teachers.view')
        <a href="{{ route('campus.teachers.pdfs') }}" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-red-100 rounded-lg mr-4">
                    <i class="bi bi-file-earmark-pdf text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Documents PDF</h3>
                    <p class="text-sm text-gray-600">Generar i gestionar PDFs</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Accedir a documents
            </div>
        </a>
        @endcan
        
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
                    <p class="text-sm text-gray-600">Gestionar pagaments de professorat</p>
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
                    <h3 class="text-xl font-semibold text-gray-900">Professorat</h3>
                    <p class="text-sm text-gray-600">Gestió completa del professorat</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Gestió de professorat
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
</div>

<!-- <div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">
       {{ __('campus.treasury') }} 
    </h3>
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
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="bi bi-credit-card text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
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
</div> -->
