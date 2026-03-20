{{-- resources/views/components/dashboard/widgets/secretaria_certificates.blade.php --}}

@isset($stats['total_certificates'])
<div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-award text-green-600 mr-2"></i>
            Certificats
        </h3>
        <div class="flex items-center">
            <span class="text-sm text-gray-500 mr-2">Emitits:</span>
            <span class="text-2xl font-bold text-green-900">{{ $stats['total_certificates'] ?? 0 }}</span>
        </div>
    </div>
    
    <div class="space-y-3">
        <!-- Certificate Types -->
        @if(isset($stats['certificates_by_type']))
            <div class="space-y-2">
                @foreach($stats['certificates_by_type'] as $type => $count)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">{{ $type }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        @endif
        
        <!-- Recent Certificates -->
        @if(isset($stats['recent_certificates']) && count($stats['recent_certificates']) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Emitits Recentment</h4>
                <div class="space-y-2">
                    @foreach($stats['recent_certificates'] as $certificate)
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $certificate->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $certificate->student->name }} - {{ $certificate->issued_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <a href="{{ route('certificates.download', $certificate) }}" 
                               class="ml-2 text-green-600 hover:text-green-800 text-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Statistics -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-900">{{ $stats['certificates_this_month'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Aquest mes</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-900">{{ $stats['certificates_this_year'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Aquest any</div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" disabled>
                    <i class="bi bi-plus-circle mr-2"></i>
                    Nou Certificat (Proximament)
                </button>
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" disabled>
                    <i class="bi bi-folder-open mr-2"></i>
                    Veure Tots (Proximament)
                </button>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                Mòdul de certificats en desenvolupament...
            </p>
        </div>
    </div>
</div>
@endif
