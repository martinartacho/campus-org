{{-- resources/views/components/admin-dashboard-cards.blade.php --}}
@auth
    @php
        $user = Auth::user();
        // Verificar si el usuario tiene permisos de administración
        $hasAdminAccess = $user->can('users.view') || 
                          $user->can('roles.index') || 
                          $user->can('permissions.index') ||
                          $user->canany(['events.view', 'event_types.view', 'event_questions.view', 'event_answers.view']) ||
                          $user->can('campus.categories.view') ||
                          $user->can('campus.seasons.view') ||
                          $user->can('campus.courses.view') ||
                          $user->can('campus.students.view') ||
                          $user->can('campus.teachers.view') ||
                          $user->can('campus.registrations.view') ||
                          $user->can('settings.edit');
    @endphp
    
    @if($hasAdminAccess)
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-shield-check me-2"></i>
                {{ __('Administració del sistema') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                
                {{-- Gestión de Usuarios --}}
                @can('users.view')
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg me-3">
                            <i class="bi bi-people text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('site.Users') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Gestió d\'usuaris del sistema') }}</p>
                        </div>
                    </div>
                </a>
                @endcan
                
                {{-- Gestión de Roles --}}
                @can('roles.index')
                <a href="{{ route('admin.roles.index') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg me-3">
                            <i class="bi bi-person-badge text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('site.Roles') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Gestió de rols d\'usuari') }}</p>
                        </div>
                    </div>
                </a>
                @endcan
                
                {{-- Gestión de Permisos --}}
                @can('permissions.index')
                <a href="{{ route('admin.permissions.index') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg me-3">
                            <i class="bi bi-key text-yellow-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('site.Permissions') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Gestió de permisos del sistema') }}</p>
                        </div>
                    </div>
                </a>
                @endcan
                
                {{-- Gestión de Feedback --}}
                @can('users.view')
                <a href="{{ route('admin.feedback.index') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg me-3">
                            <i class="bi bi-chat-left-text text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('site.Feedback') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Consultar feedback dels usuaris') }}</p>
                        </div>
                    </div>
                </a>
                @endcan
                
                {{-- Gestión de Eventos --}}
                @canany(['events.view', 'event_types.view', 'event_questions.view', 'event_answers.view'])
                <a href="{{ route('admin.events.index') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg me-3">
                            <i class="bi bi-calendar-event text-red-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('site.Events') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Gestió d\'esdeveniments') }}</p>
                        </div>
                    </div>
                </a>
                @endcanany
                                
                {{-- Gestión de Profesores --}}
                @can('campus.teachers.view')
                <a href="{{ route('campus.teachers.index') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-teal-100 rounded-lg me-3">
                            <i class="bi bi-person-workspace text-teal-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('Professors') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Gestió del professorat') }}</p>
                        </div>
                    </div>
                </a>
                @endcan
                
                {{-- Gestión de Tesorería --}}
                @canany(['campus.payments.view', 'campus.teachers.financial_data.view'])
                <a href="{{ route('treasury.dashboard') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 rounded-lg me-3">
                            <i class="bi bi-cash-stack text-emerald-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('Tresoreria') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Gestió financera i pagaments') }}</p>
                        </div>
                    </div>
                </a>
                @endcanany
                
                {{-- Configuración del Sistema --}}
                @can('settings.edit')
                <a href="{{ route('settings.edit') }}" 
                   class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                    <div class="flex items-center">
                        <div class="p-2 bg-gray-100 rounded-lg me-3">
                            <i class="bi bi-sliders text-gray-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('site.Settings') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Configuració general del sistema') }}</p>
                        </div>
                    </div>
                </a>
                @endcan
                
            </div>
        </div>
    @endif

    {{-- Gestión del Campus --}}
    @if($user->can('campus.categories.view') || $user->can('campus.seasons.view') || 
        $user->can('campus.courses.view') || $user->can('campus.students.view') ||
        $user->can('campus.teachers.view') || $user->can('campus.registrations.view'))
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-mortarboard me-2"></i>
            {{ __('Administració del Campus') }}
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Categorías --}}
            @can('campus.categories.view')
            <a href="{{ route('campus.categories.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 rounded-lg me-3">
                        <i class="bi bi-tags text-indigo-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Categories') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Gestió de categories') }}</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Temporadas --}}
            @can('campus.seasons.view')
            <a href="{{ route('campus.seasons.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg me-3">
                        <i class="bi bi-calendar-range text-orange-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Temporades') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Gestió de temporades') }}</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Cursos --}}
            @can('campus.courses.view')
            <a href="{{ route('campus.courses.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg me-3">
                        <i class="bi bi-book text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Cursos') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Gestió de cursos') }}</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Estudiantes --}}
            @can('campus.students.view')
            <a href="{{ route('campus.students.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg me-3">
                        <i class="bi bi-mortarboard text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Estudiants') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Gestió d\'estudiants') }}</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Profesores --}}
            @can('campus.teachers.view')
            <a href="{{ route('campus.teachers.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-teal-100 rounded-lg me-3">
                        <i class="bi bi-person-workspace text-teal-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Professors') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Gestió del professorat') }}</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Matriculaciones --}}
            @can('campus.registrations.view')
            <a href="{{ route('campus.registrations.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg me-3">
                        <i class="bi bi-clipboard-check text-purple-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Matriculacions') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Gestió de matriculacions') }}</p>
                    </div>
                </div>
            </a>
            @endcan
            
        </div>
    </div>
    @endif
@endauth