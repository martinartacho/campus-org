{{-- resources/views/components/dashboard/widgets/secretaria_certificates.blade.php --}}

@isset($stats['total_certificates'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-award text-green-600 me-2"></i>
        Certificats
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- TOTAL CERTIFICATES --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-800">Emitits</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['total_certificates'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="bi bi-award text-green-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-green-700">Certificats totals</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-green-200">
                <span class="text-xs text-green-600">
                    Històric complet
                </span>
            </div>
        </div>
        
        {{-- THIS MONTH --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-800">Aquest mes</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['certificates_this_month'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="bi bi-calendar-month text-blue-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-blue-700">{{ now()->format('F') }}</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-blue-200">
                <span class="text-xs text-blue-600">
                    Mensual
                </span>
            </div>
        </div>
        
        {{-- THIS YEAR --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-800">Aquest any</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['certificates_this_year'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="bi bi-calendar-year text-purple-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-purple-700">{{ now()->format('Y') }}</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-purple-200">
                <span class="text-xs text-purple-600">
                    Anual
                </span>
            </div>
        </div>
        
        {{-- CERTIFICATE TYPES --}}
        @if(isset($stats['certificates_by_type']) && count($stats['certificates_by_type']) > 0)
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-800">Tipus</p>
                        <p class="text-2xl font-bold text-orange-900">{{ count($stats['certificates_by_type']) }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="bi bi-tags text-orange-600 text-xl"></i>
                    </div>
                </div>
                
                @if(isset($stats['certificates_by_type']))
                    @php
                        $mostCommonType = collect($stats['certificates_by_type'])->sortByDesc(function($count) {
                            return $count;
                        })->keys()->first();
                    @endphp
                    <div class="mt-2 text-xs">
                        <span class="text-orange-700">Més comú: {{ $mostCommonType ?? 'N/A' }}</span>
                    </div>
                @endif
                
                <div class="mt-3 pt-2 border-t border-orange-200">
                    <span class="text-xs text-orange-600">
                        Categories
                    </span>
                </div>
            </div>
        @endif
        
    </div>
    
    {{-- CERTIFICATE TYPES DETAIL --}}
    @if(isset($stats['certificates_by_type']) && count($stats['certificates_by_type']) > 0)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Certificats per tipus</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($stats['certificates_by_type'] as $type => $count)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-900 truncate">{{ $type }}</span>
                        <span class="text-sm font-bold text-green-600">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- RECENT CERTIFICATES --}}
    @if(isset($stats['recent_certificates']) && count($stats['recent_certificates']) > 0)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Emitits recentment</h4>
            <div class="space-y-2">
                @foreach($stats['recent_certificates'] as $certificate)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $certificate->title ?? 'Certificat' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $certificate->student->name ?? 'Estudiant' }} - {{ $certificate->issued_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @if(isset($certificate->download_url))
                            <a href="{{ $certificate->download_url }}" 
                               class="ml-3 text-green-600 hover:text-green-800 text-sm p-1">
                                <i class="bi bi-download"></i>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- DEVELOPMENT NOTICE --}}
    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="bi bi-info-circle text-amber-600 mr-2"></i>
                <div>
                    <p class="text-sm font-medium text-amber-800">Mòdul en desenvolupament</p>
                    <p class="text-xs text-amber-700 mt-1">
                        Les funcions de creació i gestió de certificats estan sent desenvolupades.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
