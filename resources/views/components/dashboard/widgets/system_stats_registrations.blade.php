{{-- Widget System Stats: Registrations --}}
@isset($stats['total_registrations'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-person-check-fill text-purple-600 me-2"></i>
        Matriculacions
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- TOTAL MATRICULACIONS --}}
        <a href="{{ route('manager.registrations.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-800">Total Matriculacions</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $stats['total_registrations'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-people text-purple-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-purple-700">Historial complet</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-purple-200">
                    <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                        Gestionar matriculacions <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
        {{-- ACTIVES --}}
        @if(isset($stats['active_registrations']))
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Actives</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['active_registrations'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-green-700">Matriculacions vigents</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-green-200">
                    <span class="text-xs text-green-600">
                        Curs actiu
                    </span>
                </div>
            </div>
        @endif
        
        {{-- COMPLETADES --}}
        @if(isset($stats['completed_registrations']))
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800">Completades</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['completed_registrations'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="bi bi-award text-blue-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-blue-700">Processades correctament</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-blue-200">
                    <span class="text-xs text-blue-600">
                        Historial complet
                    </span>
                </div>
            </div>
        @endif
        
        {{-- PENDENTS --}}
        @if(isset($stats['pending_registrations']))
            <a href="{{ route('manager.registrations.index', ['status' => 'pending']) }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200 hover:border-orange-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-800">Pendents</p>
                            <p class="text-2xl font-bold text-orange-900">{{ $stats['pending_registrations'] }}</p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="bi bi-clock-history text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="mt-2 text-xs">
                        <span class="text-orange-700">Revisar urgentment</span>
                    </div>
                    
                    <div class="mt-3 pt-2 border-t border-orange-200">
                        <span class="text-xs text-orange-600 hover:text-orange-800 flex items-center">
                            Revisar pendents <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endif
        
    </div>
</div>
@endisset
