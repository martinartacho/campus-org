{{-- resources/views/components/dashboard/widgets/secretaria_registrations.blade.php --}}

@isset($stats['pending_registrations'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-person-check text-orange-600 me-2"></i>
        Matriculacions
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- PENDING REGISTRATIONS --}}
        <a href="{{ route('campus.registrations.index', ['status' => 'pending']) }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200 hover:border-yellow-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Pendents</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending_registrations'] ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="bi bi-clock-history text-yellow-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-yellow-700">Revisar urgentment</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-yellow-200">
                    <span class="text-xs text-yellow-600 hover:text-yellow-800 flex items-center">
                        Revisar pendents <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
        {{-- ACTIVE REGISTRATIONS --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-800">Actives</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['active_registrations'] ?? 0 }}</p>
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
        
        {{-- COMPLETED REGISTRATIONS --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-800">Completades</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['completed_registrations'] ?? 0 }}</p>
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
        
        {{-- ALL REGISTRATIONS --}}
        <a href="{{ route('campus.registrations.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-800">Total</p>
                        <p class="text-2xl font-bold text-purple-900">
                            {{ ($stats['pending_registrations'] ?? 0) + ($stats['active_registrations'] ?? 0) + ($stats['completed_registrations'] ?? 0) }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-list-ul text-purple-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-purple-700">Totes les matriculacions</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-purple-200">
                    <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                        Veure tots <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
    </div>
    
    {{-- DETALLES ADICIONALES --}}
    @if(isset($stats['recent_registrations']) && count($stats['recent_registrations']) > 0)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Matriculacions recents</h4>
            <div class="space-y-2">
                @foreach($stats['recent_registrations'] as $registration)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $registration->student->name ?? 'Estudiant' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $registration->course->name ?? 'Curs' }} - {{ $registration->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="ml-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                @if($registration->academic_status === 'enrolled') bg-green-100 text-green-800
                                @elseif($registration->academic_status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($registration->academic_status ?? 'unknown') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endif
