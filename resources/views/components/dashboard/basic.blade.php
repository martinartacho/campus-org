<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            basic <i class="bi bi-person"></i> {{ __('site.welcome') }}
        </h3>
        
        <div class="py-12">
           
                {{-- Estadísticas generales --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        {{ __("Benvingut/da, ") }} <strong>{{ Auth::user()->name }}</strong>!
                        {{ __("Aquí tens una visió general del teu compte.") }}
                    </div>
                </div>


                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-dashboard.card title="{{ __('site.Profile') }}" color="gray">
                        <i class="bi bi-person-circle"></i> {{ __('site.to_personal_information') }}
                    </x-dashboard.card>

                    <x-dashboard.card title="{{ __('site.Notifications') }}" color="blue">
                        <i class="bi bi-bell-fill"></i> {{ __('site.assigned_notifications') }}
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Notification::count() }}</p>
                    </x-dashboard.card>
                </div>      

                <!-- Segunda fila: Card de idioma ocupando todo el ancho -->
                    <div class="col-span-full">
                        <x-dashboard.card title="{{ __('site.Current_Language') }}" color="green">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="bi bi-translate text-xl mr-3"></i>
                                    @php
                                        // Función para obtener el nombre completo del idioma
                                        $getLanguageName = function($code) {
                                            switch($code) {
                                                case 'es': return __('site.Spanish');
                                                case 'ca': return __('site.Catalan');
                                                default: return __('site.English');
                                            }
                                        };
                                    @endphp
                                    {{ $getLanguageName(app()->getLocale()) }}
                                </div>
                                
                                @if($languageConflict = session('language_conflict'))
                                <div class="flex items-center text-yellow-600">
                                    <i class="bi bi-exclamation-triangle mr-2"></i>
                                    <span>{{ __('site.Conflict_detected') }}</span>
                                </div>
                                @endif
                            </div>
                            
                            @if($languageConflict = session('language_conflict'))
                            <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="bi bi-info-circle text-yellow-600 mr-2"></i>
                                    <span class="text-yellow-700 font-medium">
                                        {{ __('site.LanguageConflictWarning') }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                    <form method="POST" action="{{ route('language.resolve-conflict') }}" 
                                        class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                                        @csrf
                                        <input type="hidden" name="action" value="use_user">
                                        <button type="submit" class="w-full text-left">
                                            <div class="flex items-center">
                                                <i class="bi bi-arrow-repeat text-blue-500 mr-2"></i>
                                                <div>
                                                    <p class="font-medium">{{ __('site.UseMyPreferred') }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $getLanguageName($languageConflict['user_language']) }} 
                                                        ({{ __('site.Your_preference') }})
                                                    </p>
                                                </div>
                                            </div>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('language.resolve-conflict') }}" 
                                        class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                                        @csrf
                                        <input type="hidden" name="action" value="use_session">
                                        <button type="submit" class="w-full text-left">
                                            <div class="flex items-center">
                                                <i class="bi bi-check-circle text-green-500 mr-2"></i>
                                                <div>
                                                    <p class="font-medium">{{ __('site.KeepCurrent') }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $getLanguageName($languageConflict['session_language']) }} 
                                                        ({{ __('site.Current_session') }})
                                                    </p>
                                                </div>
                                            </div>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('language.resolve-conflict') }}" 
                                        class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                                        @csrf
                                        <input type="hidden" name="action" value="update_preference">
                                        <button type="submit" class="w-full text-left">
                                            <div class="flex items-center">
                                                <i class="bi bi-pencil-square text-purple-500 mr-2"></i>
                                                <div>
                                                    <p class="font-medium">{{ __('site.UpdateMyPreference') }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $getLanguageName($languageConflict['session_language']) }} 
                                                        ({{ __('site.New_preference') }})
                                                    </p>
                                                </div>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </x-dashboard.card>
                    </div>

                <!-- Tercera fila: Widget de eventos (si tiene eventos y permisos) -->
                    @isset($events)
                        @if($events->count() > 0 && Auth::user()->can('view-calendar'))
                        <div class="col-span-full mt-4">
                            <x-dashboard.card title="{{ __('site.Upcoming_Events') }}" color="purple">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($events as $event)
                                        <li class="py-3">
                                            <div class="flex space-x-3">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $event->title }}
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">
                                                        {{ \Carbon\Carbon::parse($event->start)->format('d M Y, H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                
                                <div class="mt-4">
                                    <a href="{{ route('calendar.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                        {{ __('site.View full calendar') }} →
                                    </a>
                                </div>
                            </x-dashboard.card>
                        </div>
                        @endif
                    @endisset
                    
                    <!--  fila: Card  el ancho -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                    </div>

                </div>
            </div>
    </div>
</div>