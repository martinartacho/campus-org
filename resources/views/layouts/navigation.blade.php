{{-- resources/views/layouts/navigation.blade.php (CORRECTE) --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
                {{-- Desktop section --}}
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('site.Dashboard') }}
                    </x-nav-link>
                </div>
            </div>
            @auth
                @canany(['users.view', 'roles.index', 'permissions.index', 'settings.edit'])
                    <!-- Admin Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div class="relative ms-3">
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ __('site.Admin') }}
                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>

                                <x-slot name="content">
                                    @can('users.view')
                                        <x-dropdown-link :href="route('admin.feedback.index')" :active="request()->routeIs('admin.feedback.index.*')">
                                            {{ __('site.Feedback') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @can('users.view')
                                        <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                            {{ __('site.Users') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @can('roles.index')
                                        <x-dropdown-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                                            {{ __('site.Roles') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @can('permissions.index')
                                        <x-dropdown-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')">
                                            {{ __('site.Permissions') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @canany(['events.view', 'event_types.view', 'event_questions.view', 'event_answers.view'])
                                    <x-dropdown-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                                        {{ __('site.Events') }}
                                    </x-dropdown-link>
                                    @endcanany
                                    @can('settings.edit')
                                        <x-dropdown-link :href="route('settings.edit')" :active="request()->routeIs('settings.*')">
                                            {{ __('site.Settings') }}
                                        </x-dropdown-link>
                                    @endcan

                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                @endcanany
            @endauth

            <!-- Icono notificaciones en escritorio -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <ul class="navbar-nav ms-auto">
                    @include('components.notification-bell') 
                </ul>
            </div>    

            
            <!-- Menús dinàmics per a permisos -->
            <div class="flex items-center space-x-4">
               
                {{-- Menú de Campus (DESKTOP) --}}
                {{-- @include('components.menu-campus', ['desktop' => true]) --}}
                
                {{-- Menú d'Admin (DESKTOP) --}}
            {{--     @include('components.menu-admin', ['desktop' => true]) --}}
                
                {{-- Menú d'Usuari (DESKTOP) --}}
                <!-- Role Switcher (unificado para todos los usuarios) -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @if(auth()->check() && auth()->user()->roles->count() > 1)
                        {{-- Multi-rol: Selector completo --}}
                        <div x-data="{ open: false }" class="relative">
                            <button 
                                @click="open = !open" 
                                class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <i class="bi bi-person-circle"></i>
                                <span>{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down text-xs"></i>
                            </button>

                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2 w-64 bg-white rounded-md shadow-lg border border-gray-200"
                            >
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">Hola, {{ auth()->user()->first_name ?? auth()->user()->name }}!</p>
                                        <p class="text-xs text-gray-500">Gestiona els teus rols</p>
                                    </div>
                                    
                                    @foreach(auth()->user()->roles as $role)
                                        <a href="{{ route('dashboard.switch.role', $role->name) }}" 
                                           class="flex items-center px-4 py-3 text-sm hover:bg-gray-50 transition-colors">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">{{ ucfirst($role->name) }}</div>
                                            </div>
                                            @if(session('active_role') == $role->name)
                                                <i class="bi bi-check-circle text-green-600"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                    
                                    <div class="border-t border-gray-200 mt-2 pt-2">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="bi bi-gear me-2"></i> Configuració del compte
                                        </a>
                                        
                                        @if(auth()->user()->teacherProfile)
                                        <a href="{{ route('teacher.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="bi bi-person-badge me-2"></i> Dades del Professor
                                        </a>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="bi bi-box-arrow-right me-2"></i> Tancar sessió
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Single-rol: Perfil original --}}
                        @include('components.menu-user', ['desktop' => true])
                    @endif
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (MÒBIL) -->
   {{-- Mobile section --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('site.Dashboard') }}
            </x-responsive-nav-link>

            {{-- Icono de notificaciones en móvil --}}
            <div class="border-t border-gray-200 mt-2">
                <ul class="px-4 py-2">
                    @include('components.notification-bell')
                </ul>
            </div>
         
            {{-- Menú de Campus (MÒBIL) --}}
           {{--  @include('components.menu-campus', ['desktop' => false]) --}}
            
            {{-- Menú d'Admin (MÒBIL) --}}
          {{--   @include('components.menu-admin', ['desktop' => false]) --}}
            
            {{-- Selector de roles (MÒBIL) --}}
            @if(auth()->check() && auth()->user()->roles->count() > 1)
            <div class="border-t border-gray-200 mt-2">
                <div class="px-4 py-3">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="text-sm font-medium text-blue-900 mb-2">Rol Activo: {{ ucfirst(session('active_role', auth()->user()->roles->first()->name)) }}</div>
                        <div class="flex flex-col space-y-2">
                            @foreach(auth()->user()->roles as $role)
                                <a href="{{ route('dashboard.switch.role', $role->name) }}" 
                                   class="px-3 py-2 text-xs rounded @if(session('active_role') == $role->name) bg-blue-600 text-white @else bg-white text-blue-600 border border-blue-300 @endif hover:bg-blue-100 flex items-center justify-between">
                                    <span>{{ ucfirst($role->name) }}</span>
                                    @if(session('active_role') == $role->name)
                                        <i class="bi bi-check-circle text-xs"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            {{-- Menú d'User (MÒBIL) --}}
            @include('components.menu-user', ['desktop' => false])

            
        </div>

        <!-- Responsive Settings Options -->
{{--             <div class="pt-2 pb-3 space-y-1">
            </div> --}}
    </div>
</nav>