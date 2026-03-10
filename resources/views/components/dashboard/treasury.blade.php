@props([
    'stats' => [],
    'debug' => null,
    'error' => null,
])

<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">
       {{ __('campus.treasury_manager') }} 
    </h3>

    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200 hover:border-green-300">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-green-800">{{ __('Estadístiques') }}</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
            @isset($stats['payments'])
                <span class="text-green-700">{{ __('campus.payments') }}: {{ $stats['payments'] }}</span>
            @endisset

            @isset($stats['pending_payments'])
                <span class="text-green-700">{{ __('campus.pending_payments') }}: {{ $stats['pending_payments'] }}</span>
            @endisset

            @isset($stats['teachers'])
                <span class="text-green-700">{{ __('campus.teachers') }}: {{ $stats['teachers'] }}</span>
            @endisset
        </div>
    </div>
</div> 

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- PAGAMENTS --}}
        @can('campus.payments.view')
            <a href="{{ route('treasury.payments.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">{{ __('campus.payments') }}</p>
                            <p class="text-2xl font-bold text-blue-900">
                                @isset($stats['payments'])
                                {{ $stats['payments'] }}
                                @endisset
                            </p>
                        </div>
                        <div class="p-2 bg-blue-200 rounded-lg">
                            <i class="bi bi-cash text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-green-700">Pendents: {{ $stats['pending_payments'] ?? 0 }}</span>
                        <span class="text-green-700">Aprovats: {{ $stats['approved_payments'] ?? 0 }}</span>
                    </div>
                    
                    <div class="mt-3 pt-2 border-t border-blue-200">
                        <span class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                            Gestionar pagaments <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan
        
        {{-- PROFESSORS --}}
        @can('campus.teachers.view')
            <a href="{{ route('treasury.teachers.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-800">{{ __('campus.teachers') }}</p>
                            <p class="text-2xl font-bold text-purple-900">
                                @isset($stats['teachers'])
                                {{ $stats['teachers'] }}
                                @endisset
                            </p>
                        </div>
                        <div class="p-2 bg-purple-200 rounded-lg">
                            <i class="bi bi-people text-purple-600 text-xl"></i>
                        </div>
                    </div>
                
                    <div class="mt-3 pt-2 border-t border-purple-200">
                        <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                            Gestionar professors <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan

        {{-- NOTIFICACIONS --}}
        @can('notifications.view')
            <a href="{{ route('notifications.index') }}" class="block transition-transform hover:scale-[1.02]">
                <x-dashboard.card title="{{ __('site.Notifications') }}" color="blue">
                    <i class="bi bi-bell-fill"></i> {{ __('site.assigned_notifications') }}
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->where('is_published', true)->count();
                    @endphp
                    <p class="text-2xl font-semibold text-gray-900">{{ $unreadCount }}</p>
                </x-dashboard.card>
            </a>
        @endcan

        {{-- INFORMES --}}
        @can('campus.reports.financial')
            <a href="{{ route('treasury.reports.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200 hover:border-orange-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-800">{{ __('campus.reports') }}</p>
                            <p class="text-2xl font-bold text-orange-900">
                                <i class="bi bi-file-earmark-text"></i>
                            </p>
                        </div>
                    </div>
                
                    <div class="mt-3 pt-2 border-t border-orange-200">
                        <span class="text-xs text-orange-600 hover:text-orange-800 flex items-center">
                            Veure informes <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endcan
    </div>
</div>
