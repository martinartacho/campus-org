{{-- resources/views/components/dashboard/widgets/secretaria_registrations.blade.php --}}

@isset($stats['pending_registrations'])
<div class="bg-white shadow rounded-lg p-6 border-l-4 border-orange-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-person-check text-orange-600 mr-2"></i>
            Matriculacions
        </h3>
        <div class="flex items-center">
            <span class="text-sm text-gray-500 mr-2">Pendents:</span>
            <span class="text-2xl font-bold text-orange-900">{{ $stats['pending_registrations'] ?? 0 }}</span>
        </div>
    </div>
    
    <div class="space-y-3">
        <!-- Registration Status -->
        @if(isset($stats['registrations_by_status']))
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-900">{{ $stats['active_registrations'] ?? 0 }}</div>
                    <div class="text-sm text-green-700">Actives</div>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-900">{{ $stats['completed_registrations'] ?? 0 }}</div>
                    <div class="text-sm text-yellow-700">Completades</div>
                </div>
            </div>
        @endif
        
        <!-- Recent Registrations -->
        @if(isset($stats['recent_registrations']) && count($stats['recent_registrations']) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Matriculacions Recents</h4>
                <div class="space-y-2">
                    @foreach($stats['recent_registrations'] as $registration)
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $registration->student->name ?? 'Estudiant' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $registration->course->name ?? 'Curs' }} - {{ $registration->created_at->format('d/m/Y') }}
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
        
        <!-- Quick Actions -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex space-x-2">
                <a href="{{ route('campus.registrations.index', ['status' => 'pending']) }}" 
                   class="inline-flex items-center px-3 py-2 border border-orange-300 text-sm font-medium rounded-md text-orange-700 bg-orange-50 hover:bg-orange-100">
                    <i class="bi bi-clock-history mr-2"></i>
                    Revisar Pendents
                </a>
                <a href="{{ route('campus.registrations.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="bi bi-list-ul mr-2"></i>
                    Veure Totes
                </a>
            </div>
        </div>
    </div>
</div>
@endif
