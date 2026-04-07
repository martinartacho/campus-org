@props([
    'stats' => [],
    'debug' => null,
    'error' => null,
])

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="bi bi-cash-stack me-3"></i>
            {{ __('campus.treasury') }} 
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

