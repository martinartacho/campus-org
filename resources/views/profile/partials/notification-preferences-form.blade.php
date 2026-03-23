<div class="space-y-6">
    <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preferències de Notificació') }}
        </h2>
    </div>

    @if (session('status') == 'notifications-updated')
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ __('Les teves preferències de notificació s\'han actualitzat correctament!') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.notifications.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Email/Web Preferences -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="bi bi-gear mr-2"></i>{{ __('Preferències Generals') }}
            </h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Activar notificacions per email') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_email_enabled" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_email_enabled'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_email_enabled" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_email_enabled'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Activar notificacions web (campana)') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_web_enabled" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_web_enabled'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_web_enabled" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_web_enabled'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Notifications -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="bi bi-headset mr-2"></i>{{ __('Notificacions de Suport') }}
            </h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Rebre emails de confirmació de sol·licituds de suport') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_support_email" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_support_email'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_support_email" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_support_email'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Rebre notificacions web de sol·licituds de suport') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_support_web" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_support_web'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_support_web" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_support_web'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Notifications -->
        @if(auth()->user()->hasAnyRole(['admin', 'gestio', 'coordinacio', 'comunicacio', 'secretaria', 'editor', 'treasury', 'junta']))
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="bi bi-building mr-2"></i>{{ __('Notificacions de Departament') }}
            </h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Rebre emails de noves sol·licituds del departament') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_department_email" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_department_email'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_department_email" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_department_email'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Rebre notificacions web de noves sol·licituds del departament') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_department_web" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_department_web'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_department_web" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_department_web'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Notifications -->
        @if(auth()->user()->hasAnyRole(['admin', 'gestio', 'coordinacio', 'comunicacio', 'secretaria', 'editor', 'treasury', 'junta']))
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="bi bi-shield-check mr-2"></i>{{ __('Notificacions d\'Admin') }}
            </h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Rebre emails de seguiment administratiu') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_admin_email" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_admin_email'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_admin_email" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_admin_email'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <span class="text-sm text-gray-700 mb-2 block">
                        {{ __('Rebre notificacions web de seguiment administratiu') }}
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_admin_web" 
                                   value="1"
                                   {{ $notificationPreferences['notifications_admin_web'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="notifications_admin_web" 
                                   value="0"
                                   {{ !$notificationPreferences['notifications_admin_web'] ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @else
        <!-- Message for non-management roles -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="bi bi-info-circle text-blue-600 mr-3 mt-1"></i>
                <div>
                    <h4 class="text-sm font-medium text-blue-800">{{ __('Preferències Bàsiques') }}</h4>
                    <p class="mt-1 text-xs text-blue-700">
                        {{ __('Com a professor/a o alumne/a, només pots configurar les notificacions generals i de suport. Les notificacions de departament i admin estan reservades per a rols de gestió.') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Frequency Preferences -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="bi bi-clock mr-2"></i>{{ __('Freqüència de Recepció') }}
            </h3>
            
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="radio" 
                           name="notifications_frequency" 
                           value="immediate"
                           {{ $notificationPreferences['notifications_frequency'] == 'immediate' ? 'checked' : '' }}
                           class="border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>{{ __('Immediata') }}</strong> - {{ __('Rebre notificacions a l\'instant') }}
                    </span>
                </label>

                <label class="flex items-center">
                    <input type="radio" 
                           name="notifications_frequency" 
                           value="daily"
                           {{ $notificationPreferences['notifications_frequency'] == 'daily' ? 'checked' : '' }}
                           class="border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>{{ __('Diària') }}</strong> - {{ __('Rebre un resum cada dia a les 9:00') }}
                        <span class="text-xs text-gray-500 block ml-0">{{ __('(Properament disponible)') }}</span>
                    </span>
                </label>

                <label class="flex items-center">
                    <input type="radio" 
                           name="notifications_frequency" 
                           value="weekly"
                           {{ $notificationPreferences['notifications_frequency'] == 'weekly' ? 'checked' : '' }}
                           class="border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>{{ __('Setmanal') }}</strong> - {{ __('Rebre un resum cada dilluns a les 9:00') }}
                        <span class="text-xs text-gray-500 block ml-0">{{ __('(Properament disponible)') }}</span>
                    </span>
                </label>
            </div>
        </div>

        <!-- Critical Notifications Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-info-circle text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        {{ __('Notificacions Crítiques') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        {{ __('Les notificacions de seguretat i urgències crítiques sempre s\'enviaran a tots els usuaris pertinents per garantir la seguretat del sistema.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                <i class="bi bi-check-lg mr-2"></i>
                {{ __('Desar Preferències') }}
            </button>
        </div>
    </form>
</div>
